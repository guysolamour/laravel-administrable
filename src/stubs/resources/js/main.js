$("form [required]").each(function (index, item) {
  const element = $(item)
  let label = $("label[for='" + element.attr('id') + "']");

  if (label) {
    let labelText = label.text();
    label.html(labelText += ' <span class="text-danger">*</span>');
  }

  if (element.prop('type') === 'textarea') {
    element.removeAttr('required')
  }

});
