/********************************************************************************/
/* Show / Hide Element: ToDO: Nicer slideDown */
/********************************************************************************/
function showDiv(sDivId)
{
  $(sDivId).style.display = "block";
}

function hideDiv(sDivId)
{
  new Fx.Slide(sDivId).toggle().hide();
}

/* Automatically hide success message */
if($('success'))
{
  showDiv('flashMessage');
  new Fx.Slide('success').hide().toggle();
}

if($('error'))
{
  showDiv('flashMessage');
  new Fx.Slide('error').hide().toggle();
}

/********************************************************************************/
/* Quote messages in Comments */
/********************************************************************************/
function quoteMessage(sNames, sDivId)
{
  var sMessage = document.getElementById(sDivId).innerHTML;
  var sHTML = "[quote=" + sNames + "]" + sNewMessage + "[/quote]\n";

  document.getElementById('createCommentText').value = sHTML;
  return false;
}

/********************************************************************************/
/* global confirmations */
/********************************************************************************/
function confirmDelete(sTitle, sUrl)
{
  if( confirm("Are you sure to delete " + sTitle + "?") )
    parent.location.href = sUrl;
}

/********************************************************************************/
/* Tooltips */
/********************************************************************************/
/* Show Tooltips on Blog */
window.addEvent('domready', function() {
  if($$('.tooltip'))
  {
    $$('.tooltip').each(function(element,index) {
      var content = element.get('title').split('::');
      element.store('tip:title', content[0]);
      element.store('tip:text', content[1]);
    });

    var myTips = new Tips('.tooltip');
    myTips.addEvent('show', function(tip){
      tip.fade('in');
    });
    myTips.addEvent('hide', function(tip){
      tip.fade('out');
    });
  }
})

/********************************************************************************/
/* Avoid Alphachars in FileUpload */
/********************************************************************************/
function stripNoAlphaChars(sValue)
{
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

function stripSlash(sValue)
{
  sValue = sValue.replace(/\//g, "&frasl;");
  return sValue;
}

function reloadPage(sURL, sRoot)
{
  var sId = 'js-ajax_reload';
  $(sId).set('html', "<img src='" + sRoot + "/slimbox/loading.gif' alt='loading...' />");
  $(sId).load(sURL);
}