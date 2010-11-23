window.addEvent('domready', function() {
  each(document.search('.js-image_overlay'), function(el) {
    var oSize = el.getSize();
    el.setStyle('margin-top', '-' + oSize.y + 'px');
    el.fade('hide');
  });
});

/********************************************************************************/
/* Show / Hide Element */
/********************************************************************************/
function hideDiv(sDivId) {
  new Fx.Slide(sDivId).slideOut();
}

function fadeDiv(sDivId) {
  document.id(sDivId).fade('toggle');
}

function imageOverlay(sDivId, iHeight) {
  fadeDiv(sDivId);
  document.id(sDivId).setStyle('margin-top', iHeight);
}

function showDiv(sDivId) {
  document.id(sDivId).setStyle('display', 'inline');

  if(document.id('js-flash_success') || document.id('js-flash_error')) {
    (function(){
      hideDiv(sDivId)
    }).delay(5000);
  }
}

if(document.id('js-flash_success') || document.id('js-flash_error')) {
  showDiv('js-flash_message');
}

/********************************************************************************/
/* Quote messages in Comments */
/********************************************************************************/
function quoteMessage(sName, sDivId) {
  var sMessage = document.id(sDivId).get('html');
  var sQuote = "[quote=" + sName + "]" + sMessage + "[/quote]\n";
  var sOldMessage = $('js-create_commment_text').get('value');
  document.id('js-create_commment_text').set('html', sOldMessage + sQuote);
  return false;
}

function resetContent(sDivId) {
  document.id(sDivId).set('html', '');
}

/********************************************************************************/
/* global confirmations */
/********************************************************************************/
function confirmDelete(sUrl) {
  if( confirm(LANG_DELETE_FILE_OR_CONTENT) )
    parent.location.href = sUrl;
}

/********************************************************************************/
/* Tooltips */
/********************************************************************************/
/* Show Tooltips on Blog */
if(document.search('.js-tooltip')) {
  document.search('.js-tooltip').each(function(element, index) {
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
  document.id(sId).setStyle('display', 'block').addClass('center');
  document.id(sId).set('html', "<img class='js-loading' src='" + sRoot + "/loading.gif' alt='" + LANG_LOADING + "' />");
  document.id(sId).load(sURL);
}

function checkPasswords() {
  if(document.id('password') && document.id('password2')) {
    if( document.id('password').value == document.id('password2').value ) {
      document.id('icon').set('class', 'icon-success');
    }
    else {
      document.id('icon').set('class', 'icon-close');
    }
  }
}