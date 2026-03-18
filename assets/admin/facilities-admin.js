document.addEventListener('DOMContentLoaded', function () {
  var deleteText = (window.jpafAdmin && window.jpafAdmin.deleteConfirm) || 'Delete this item?';
  var chooseSvgText = (window.jpafAdmin && window.jpafAdmin.chooseSvg) || 'Choose SVG icon';
  var useThisSvgText = (window.jpafAdmin && window.jpafAdmin.useThisSvg) || 'Use this SVG';

  document.querySelectorAll('.jpaf-delete-link').forEach(function (link) {
    link.addEventListener('click', function (event) {
      if (!window.confirm(deleteText)) {
        event.preventDefault();
      }
    });
  });

  var iconType = document.getElementById('jpaf-icon-type');
  var iconValue = document.getElementById('jpaf-icon-value');
  var iconAttachment = document.getElementById('jpaf-icon-attachment-id');
  var uploadButton = document.querySelector('.jpaf-upload-svg-button');
  var preview = document.querySelector('.jpaf-icon-preview');
  var sortableBody = window.jQuery ? window.jQuery('#jpaf-sortable-library') : null;
  var sortedInput = document.getElementById('jpaf-sorted-ids');
  var groupCategoryFilter = document.getElementById('jpaf-facility-category-filter');

  function escapeAttr(value) {
    return String(value || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function renderIconPreview() {
    if (!preview || !iconType || !iconValue) {
      return;
    }

    var type = iconType.value;
    var value = (iconValue.value || '').trim();
    var dashiconHelp = document.querySelector('.jpaf-help-dashicon');
    var customHelp = document.querySelector('.jpaf-help-custom');
    var svgHelp = document.querySelector('.jpaf-help-svg');

    [dashiconHelp, customHelp, svgHelp].forEach(function (el) {
      if (el) {
        el.style.display = 'none';
      }
    });

    if (uploadButton) {
      uploadButton.style.display = type === 'svg' ? 'inline-flex' : 'none';
    }

    if (type === 'custom_class') {
      if (customHelp) customHelp.style.display = 'block';
      preview.innerHTML = value ? '<span class="jpaf-facility__icon-inner" aria-hidden="true"><i class="' + escapeAttr(value) + '"></i></span>' : '<span class="dashicons dashicons-admin-site" aria-hidden="true"></span>';
      return;
    }

    if (type === 'svg') {
      if (svgHelp) svgHelp.style.display = 'block';
      preview.innerHTML = value ? '<span class="jpaf-facility__icon-inner jpaf-facility__icon-inner--svg" aria-hidden="true"><img src="' + escapeAttr(value) + '" alt="" /></span>' : '<span class="dashicons dashicons-format-image" aria-hidden="true"></span>';
      return;
    }

    if (iconAttachment) {
      iconAttachment.value = '';
    }

    if (dashiconHelp) dashiconHelp.style.display = 'block';
    var dashClass = value && value.indexOf('dashicons-') === 0 ? value : 'dashicons-admin-site';
    preview.innerHTML = '<span class="jpaf-facility__icon-inner dashicons ' + escapeAttr(dashClass) + '" aria-hidden="true"></span>';
  }

  if (iconType) {
    iconType.addEventListener('change', renderIconPreview);
  }

  if (iconValue) {
    iconValue.addEventListener('input', renderIconPreview);
  }

  if (uploadButton && window.wp && window.wp.media) {
    uploadButton.addEventListener('click', function (event) {
      event.preventDefault();
      var frame = window.wp.media({
        title: chooseSvgText,
        button: { text: useThisSvgText },
        library: { type: 'image' },
        multiple: false
      });

      frame.on('select', function () {
        var attachment = frame.state().get('selection').first().toJSON();
        if (!attachment || !attachment.url) {
          return;
        }
        if (iconType) {
          iconType.value = 'svg';
        }
        if (iconValue) {
          iconValue.value = attachment.url;
        }
        if (iconAttachment) {
          iconAttachment.value = attachment.id || '';
        }
        renderIconPreview();
      });

      frame.open();
    });
  }

  if (sortableBody && sortableBody.length && typeof sortableBody.sortable === 'function') {
    function updateSortedIds() {
      if (!sortedInput) {
        return;
      }
      var ids = sortableBody.find('> tr').map(function () {
        return this.getAttribute('data-id');
      }).get();
      sortedInput.value = ids.join(',');
    }

    sortableBody.sortable({
      items: '> tr',
      axis: 'y',
      handle: '.jpaf-drag-handle',
      placeholder: 'jpaf-sort-placeholder',
      forcePlaceholderSize: true,
      tolerance: 'pointer',
      helper: function (e, ui) {
        ui.children().each(function () {
          window.jQuery(this).width(window.jQuery(this).outerWidth());
        });
        return ui;
      },
      start: function (e, ui) {
        ui.placeholder.height(ui.helper.outerHeight());
      },
      update: updateSortedIds
    });

    updateSortedIds();
  }

  function applyGroupCategoryFilter() {
    if (!groupCategoryFilter) {
      return;
    }
    var selected = groupCategoryFilter.value || '';
    document.querySelectorAll('.jpaf-checkbox-item[data-category-id]').forEach(function (item) {
      var itemCategory = item.getAttribute('data-category-id') || '';
      item.style.display = (!selected || itemCategory === selected) ? '' : 'none';
    });
  }

  if (groupCategoryFilter) {
    groupCategoryFilter.addEventListener('change', applyGroupCategoryFilter);
    applyGroupCategoryFilter();
  }

  renderIconPreview();

  if (window.location.hash === '#jpaf-form-card') {
    var formCard = document.getElementById('jpaf-form-card');
    if (formCard) {
      formCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  }
});
