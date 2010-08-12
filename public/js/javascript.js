/********************************************************************************/
/* Show / Hide Element: ToDO: Nicer slideDown */
/********************************************************************************/
function hideDiv(sDivId) {
  new Fx.Slide(sDivId).toggle();
}

function fadeDiv(sDivId) {
  $(sDivId).fade('out');
}

function showDiv(sDivId) {
  window.addEvent('domready', function() {
    $(sDivId).setStyle('display', 'inline');
    if($('js-flash_success') || $('js-flash_error')) {
      (function(){ hideDiv(sDivId) }).delay(3000);
    }
  });
}

if($('js-flash_success') || $('js-flash_error')) {
  showDiv('js-flash_message');
}

/********************************************************************************/
/* Quote messages in Comments */
/********************************************************************************/
function quoteMessage(sName, sDivId) {
  var sMessage = $(sDivId).get('html');
  var sQuote = "[quote=" + sName + "]" + sMessage + "[/quote]\n";
  var sOldMessage = $('js-create_commment_text').get('value');
  $('js-create_commment_text').set('html', sOldMessage + sQuote);
  return false;
}

function destroyContent(sDivId) {
  $(sDivId).set('html', '');
}

/********************************************************************************/
/* global confirmations */
/********************************************************************************/
function confirmDelete(sTitle, sUrl) {
  if( confirm("Are you sure to delete " + sTitle + "?") )
    parent.location.href = sUrl;
}

/********************************************************************************/
/* Tooltips */
/********************************************************************************/
/* Show Tooltips on Blog */
if($$('.js-tooltip')) {
  $$('.js-tooltip').each(function(element, index) {
    var content = element.get('title').split('::');
    element.store('tip:title', content[0]);
    element.store('tip:text', content[1]);
  });

  var myTips = new Tips('.js-tooltip');
  myTips.addEvent('show', function(tip){
    tip.fade('in');
  });
  myTips.addEvent('hide', function(tip){
    tip.fade('out');
  });
}

/********************************************************************************/
/* Avoid Alphachars in FileUpload */
/********************************************************************************/
function stripNoAlphaChars(sValue) {
  sValue = sValue.replace(/ /g, "_");
  sValue = sValue.replace(/Ä/g, "Ae");
  sValue = sValue.replace(/ä/g, "ae");
  sValue = sValue.replace(/Ö/g, "Oe");
  sValue = sValue.replace(/ö/g, "oe");
  sValue = sValue.replace(/Ü/g, "Ue");
  sValue = sValue.replace(/ü/g, "ue");
  sValue = sValue.replace(/ß/g, "ss");
  sValue = sValue.replace(/\W/g, "_");
  return sValue;
}

function stripSlash(sValue) {
  sValue = sValue.replace(/\//g, "&frasl;");
  return sValue;
}

function reloadPage(sURL, sRoot) {
  var sId = 'js-ajax_reload';
  $(sId).set('html', "<img src='" + sRoot + "/loading.gif' alt='loading...' />");
  $(sId).load(sURL);
}

function checkPasswords() {
  if($('password') && $('password2')) {
    if( $('password').value == $('password2').value ) {
      $('icon').set('class', 'icon-success');
    }
    else {
      $('icon').set('class', 'icon-close');
    }
  }
}