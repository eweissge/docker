/*
  https://www.sitepoint.com/instant-validation/
*/

function something()
{
  //document.getElementById("email").value = "Hello world!";
  document.getElementsByName("email").value = "Hello World!";
}

function validateEmailAddress (email)
{
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}

function addEvent(node, type, callback)
{
  if (node.addEventListener)
  {
    node.addEventListener(type, function(e)
    {
      callback(e, e.target);
    }, false);
  }
  else if (node.attachEvent)
  {
    node.attachEvent('on' + type, function(e)
    {
      callback(e, e.srcElement);
    });
  }
}

function shouldBeValidated(field)
{
  return (
    !(field.getAttribute("readonly") || field.readonly) &&
    !(field.getAttribute("disabled") || field.disabled) &&
    (field.getAttribute("pattern") || field.getAttribute("required"))
  );
}

function instantValidation(field)
{
  if (shouldBeValidated(field))
  {
    var invalid =
      (field.getAttribute("required") && !field.value) ||
      (field.getAttribute("pattern") &&
        field.value &&
        !new RegExp(field.getAttribute("pattern")).test(field.value));
    if (!invalid && field.getAttribute("aria-invalid"))
    {
      field.removeAttribute("aria-invalid");
    }
    else if (invalid && !field.getAttribute("aria-invalid"))
    {
      field.setAttribute("aria-invalid", "true");
    }
  }
}

// MAIN

validateEmailAddress('eweissgerber@uwm.com');
validateEmailAddress('eric@eweissgerber@uwm.com');

//something();
