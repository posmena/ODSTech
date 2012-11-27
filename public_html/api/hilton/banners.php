var searchAsYouTypeConfiguration =  {
    // The path (beginning of the URL) to the place containing /http://s.odst.co.uk/api/hilton/images and
    // /styles. Should end with a slash. 
    // e.g. http://intranet.company.com/search-as-you-type/
    resourcesPath: 
      "/api/hilton/",

    // The fully qualified URL to the Ajax responder. 
    // e.g. http://intranet.company.com/search-as-you-type/responder.php
    ajaxResponderUrl:
      "http://s.odst.co.uk/api/hilton/search.php",

    // The fully qualified URL to the help page. Leave as empty string if
    // not available
    // e.g. http://intranet.company.com/search-as-you-type/help.html
    helpPageUrl: 
      "",

    // How many results will be shown in full. If there are more than these,
    // all but "direct hits" will be summarized. Default value: 3
    maxFullResults: 4,

    // The delay (in ms) between pressing a key (while typing in a search 
    // query) and firing the query search. Shouldn't be too big, because the 
    // users will have to wait a long time for results. Shouldn't be too small, 
    // because it will increase the load on a server. Default value: 20
    keystrokeDelay: 5,

    // The delay (in ms) between pressing a key and results being shown.
    // Shouldn't be too big, because it will be less usable, and the users 
    // will grow impatient. Shouldn't be too small, because the results will
    // flicker below as the user is typing. Please note that the actual
    // time might be bigger if the Ajax responder is slow. Default value: 200
    showResultsDelay: 10,

    // The distance (in pixels) that should be left from the bottom edge of 
    // the screen if there are many results. Default value: 10
    bottomPageMargin: 10
  };

/**
 * SearchAsYouType class.
 * @constructor
 */
function SearchAsYouType() {
}

/**
 * Initialize Search-as-you-type. This needs to be run on the page
 * using Search-as-you-type.
 *
 * @param {element} inputFieldEl An input field element Search-as-you-type
 *                               should attach itself to
 * @param {bool} focus Whether to set focus on this element
 */
SearchAsYouType.prototype.initialize = function(inputFieldEl, focus) {
  this.initializeVariables_(inputFieldEl);
  this.inputFieldEl.value = "";
  this.detectBrowser_();
  this.attachStylesheets_();
  this.createDomElements_();

  this.restoreInputField_();
  this.addEventHandlers_();
  this.prepareRandomTip_();
  this.updateDimensionsAndShadow_(null);

  if (this.debugMode) {
    this.activateDebugConsole_();
  }

  if (focus) {
    this.focusInputField_();
  }

  this.initialized = true;
}

/**
 * Initialize all the variables needed for later.
 * @param {element} inputFieldEl An input field element Search-as-you-type
 *                               should attach itself to
 */
SearchAsYouType.prototype.initializeVariables_ = function(inputFieldEl) {
  // Location (URL) of the parent page
  this.location = "" + window.location;

  // Protocol used by the parent page ("http" or "https").
  this.protocol = this.location.substr(0, this.location.indexOf("://") + 3);

  // Path (URL beginning) to resources such as http://s.odst.co.uk/api/hilton/images or CSS files
  this.resourcesPath = searchAsYouTypeConfiguration.resourcesPath;
  // (...) make it understand https

  // <script> object for Ajax calls
  this.ajaxObject = null; 

  // Results from the last search
  this.results = {}; 

  // Search cache (containing previous responses)
  this.searchCache = []; 

  // Whether the whole as-you-type search engine has been initialized
  this.initialized = false; 
  
  // Whether we are waiting for Ajax response (shows a rotating progress 
  // icon if so)
  this.waitingForSearchResults = false; 

  // Whether search results window is hidden or visible 
  this.resultsWindowHidden = true; 
  
  // A handler to the input field
  this.inputFieldEl = inputFieldEl;

  // The query last typed by the user
  this.typedQuery = this.getInputFieldValue_(); 

  // A handler to the search results window element
  this.searchResultsEl = 0; 

  // A handler to the alternate search results window (we have two and switch
  // between them for better visuals)
  this.alternateSearchResultsEl = 0; 

  // Whether the input field currently has focus (can be 0, 0.5 or 1) 
  this.inputFieldHasFocus = 0;     

  // Whether any of the results is activated by navigating through it via
  // keyboard. -1 if no, 0 or more if yes (indicates the number of the 
  // active search result)
  this.activeResult = -1; 

  // Whether the search result window has been dismissed manually by clicking
  // somewhere else
  this.resultsWindowHiddenByClicking = false;

  // Whether the arrow key has been processed on keydown event, and can be
  // ignored on keypress (see handleBodyKeyPress for more information on why
  // this is necessary)
  this.arrowKeyProcessed = false;

  // The code of the last pressed key
  this.lastKeyPressed = 0;

  // Timer id of the JavaScript timer to show results
  this.showResultsTimeoutId = -1; 

  // The id of the JavaScript timer to fire a query after 
  // searchAsYouTypeConfiguration.keystrokeDelay ms have passed 
  // since the last keystroke
  this.keystrokeTimeoutId = -1; 

  // Current autocomplete value
  this.autocomplete = '';

  // Whether autocomplete has just been collapsed (i.e. turned into regular
  // regular input text by pressing Tab or right arrow)
  this.autocompleteJustCollapsed = false;

  // Contents of the tip appearing as the last search result for 5% of the
  // queries ('' if not available)
  this.tipText = '';

  // Whether we're in the debug mode (activated by adding 
  // ?debugSearchAsYouType to the URL)
  this.debugMode = this.location.indexOf("debugSearchAsYouType") > -1;
}

/**
 * Figure out which browser is being used.
 */
SearchAsYouType.prototype.detectBrowser_ = function() {
  this.browserIE = false;
  this.browserFirefox = false;
  this.browserSafari = false;

  if (navigator.userAgent.indexOf("MSIE") > -1) {
    this.browserIE = true;
  } else if ((navigator.userAgent.indexOf("Firefox/") > -1)) {
    this.browserFirefox = true;
    if ((navigator.userAgent.indexOf("Firefox/1.0.") > -1)) {
      this.browserFirefox10 = true;
    } else {
      this.browserFirefox10 = false;
    }
  } else if (navigator.userAgent.indexOf("Safari") > -1) {
    this.browserSafari = true;
    if (navigator.userAgent.indexOf("Version/") > -1) {
      this.browserSafari3OrHigher = true;
    }
  }
}

/**
 * Attach the necessary CSS stylesheets to the document body. This adds
 * a generic CSS plus extra stylesheets containing exceptions for IE and 
 * Safari.
 */
SearchAsYouType.prototype.attachStylesheets_ = function() {
  this.attachStylesheet_('generic.css');
  this.attachStylesheet_('customized.css');
  if (this.browserIE) {
    this.attachStylesheet_('ie.css');
  } else if (this.browserSafari) {
    this.attachStylesheet_('safari.css');
  }
}

/**
 * Attach a CSS stylesheet to the document body.
 * @param {String} filename Absolute URL of the stylesheet
 */
SearchAsYouType.prototype.attachStylesheet_ = function(filename) {
  var el = document.createElement('link');
  el.href = this.resourcesPath + "styles/" + filename;
  el.type = 'text/css';
  el.rel = 'stylesheet';
  document.getElementsByTagName('head').item(0).appendChild(el);
}

/**
 * Create all the necessary page elements: search results window(s),
 * shadow elements, loading, backup input element, and autocomplete.
 */
SearchAsYouType.prototype.createDomElements_ = function() {
  // A backup input field necessary to preserve the last entry when 
  // coming back to the page -- since we're disabling browser's native
  // autocomplete on the regular input field, it will always be clean when
  // entering the page
  var el = document.createElement("input");
  el.id = 'searchAsYouTypeBackupSearchField';
  el.style.display = 'none'; // in case CSS is not yet loaded
  document.body.appendChild(el);

  // Two search results canvas windows
  this.searchResultsEl = document.createElement("ul");
  this.searchResultsEl.id = 'searchAsYouTypeResults1';
  this.searchResultsEl.className = 'jq-ui-autocomplete';
  this.searchResultsEl.style.display = 'none'; 
  this.searchResultsEl.style.position = 'absolute'; 
  this.searchResultsEl.onclick = 'alert("");event.cancelBubble = true;';
  this.searchResultsEl.tabIndex = -1;

  this.alternateSearchResultsEl = document.createElement("ul");
  this.alternateSearchResultsEl.id = 'searchAsYouTypeResults2';
  this.alternateSearchResultsEl.className = 'jq-ui-autocomplete';
  this.alternateSearchResultsEl.style.display = 'none'; 
  this.alternateSearchResultsEl.style.position = 'absolute'; 
  this.alternateSearchResultsEl.onclick = 'alert("");event.cancelBubble = true;';
  this.alternateSearchResultsEl.tabIndex = -1;

  // Shadows for the current search results canvas
  this.searchResultsShadowEl = document.createElement("div");
  this.searchResultsShadowEl.id = 'searchAsYouTypeResultsShadow';
  this.searchResultsShadowEl.style.visibility = 'hidden'; 
  this.searchResultsShadowEl.style.display = 'none'; 
  this.searchResultsShadowEl.style.left = 0; 
  this.searchResultsShadowEl.style.top = 0; 
  this.searchResultsShadowEl.style.width = 0; 
  this.searchResultsShadowEl.style.height = 0; 

  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowL';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowR';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowB';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowBL';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowBR';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowTL';
  this.searchResultsShadowEl.appendChild(el);
  var el = document.createElement("div"); 
  el.id = 'searchAsYouTypeResultsShadowTR';
  this.searchResultsShadowEl.appendChild(el);

  var el = document.createElement("searchAsYouType");
  el.id = 'searchAsYouType';

  el.appendChild(this.searchResultsEl);
  el.appendChild(this.alternateSearchResultsEl);
  el.appendChild(this.searchResultsShadowEl);
  document.body.appendChild(el);

  // Loading animation (to be position in the input field)
  this.waitingForSearchResultsEl = document.createElement("img");
  this.waitingForSearchResultsEl.style.visibility = 'hidden'; 
  this.waitingForSearchResultsEl.style.position = 'absolute'; 
  this.waitingForSearchResultsEl.src = 
    this.resourcesPath + "http://s.odst.co.uk/api/hilton/images/loading.gif";

  document.body.appendChild(this.waitingForSearchResultsEl);

  // Autocomplete element
  this.autocompleteEl = document.createElement("div");
  this.autocompleteEl.id = 'searchAsYouTypeAutocomplete';
  this.autocompleteEl.className = 'searchAsYouTypeAutocompleteInputMatch';
  document.body.appendChild(this.autocompleteEl);
  this.autocompleteEl.onmousedown = 
    searchAsYouTypeBind(this.handleAutocompleteMouseDown, this);
  this.autocompleteEl.style.zIndex = 5000;
  this.autocompleteEl.style.display = 'none';

  // Autocomplete helper, used to calculate dimensions
  this.autocompleteHelperEl = document.createElement("div");
  this.autocompleteHelperEl.id = 'searchAsYouTypeAutocompleteHelper';
  this.autocompleteHelperEl.visibility = 'hidden';
  this.autocompleteHelperEl.className = 'searchAsYouTypeAutocompleteInputMatch';
  document.body.appendChild(this.autocompleteHelperEl);
}

/**
 * Get a query from the input field and clean it up a little bit
 * @return {String} A cleaned up query
 */
SearchAsYouType.prototype.getInputFieldValue_ = function() {
if( this.inputFieldEl == null )
	{
	return "";
	}
	
  return this.inputFieldEl.value.toLowerCase().
         replace(/^\s+/g, '').replace(/\s+$/g, '');
}

/**
 * Set focus on the input field. We do some extra gymnastics here for IE
 * so that the caret ends up at the end of the input field.
 */
SearchAsYouType.prototype.focusInputField_ = function() {
  this.inputFieldEl.focus();

  if (this.inputFieldEl.createTextRange && window.document.selection) {
    var sel = this.inputFieldEl.createTextRange();
    sel.collapse(true);
    sel.move("character", this.inputFieldEl.value.length);
    sel.select();
  }
}

/**
 * Clear the input field and autocomplete. Prepares a random tip (we only do
 * it here so tips don't change or come and go as the user is typing).
 */
SearchAsYouType.prototype.clearInputField_ = function() {
  this.inputFieldEl.value = '';
  this.clearAutocomplete_(true);

  this.prepareRandomTip_();
}

/**
 * Save the contents of the input field in case the user goes back
 * to the page.
 */
SearchAsYouType.prototype.saveInputField = function(e) {
  // The main input field has browser autocomplete turned off, because
  // the auto-complete window would cover SearchAsYouType window. 
  // Unfortunately, this has another side effect -- the contents of the 
  // input field won't be retained after the user pressed back button to 
  // go back to the homepage.
  //
  // We need to copy the value to a hidden input field (but with 
  // autocomplete) and copy it back when the page loads.
  document.getElementById('searchAsYouTypeBackupSearchField').value = 
    this.inputFieldEl.value;
  document.getElementById('searchAsYouTypeBackupSearchField').
    setAttribute("active", 1);
}

/**
 * Retain the previous text entry and put focus on the input field.
 */
SearchAsYouType.prototype.restoreInputField_ = function() {
  if (document.getElementById('searchAsYouTypeBackupSearchField').
        getAttribute("active")) {
    this.inputFieldEl.value = 
      document.getElementById('searchAsYouTypeBackupSearchField').value;
    this.typedQuery = this.getInputFieldValue_();
  }
}

/**
 * Add necessary event handlers for the input field and the body of the page.
 */
SearchAsYouType.prototype.addEventHandlers_ = function() {
  // (...) event listener
  this.inputFieldEl.onkeyup = searchAsYouTypeBind(this.handleInputKeyUp, this);
  this.inputFieldEl.onkeypress = 
    searchAsYouTypeBind(this.handleInputKeyPress, this);
  this.inputFieldEl.onkeydown = 
    searchAsYouTypeBind(this.handleInputKeyDown, this);
  this.inputFieldEl.onfocus = searchAsYouTypeBind(this.handleInputFocus, this);
  this.inputFieldEl.onblur = searchAsYouTypeBind(this.handleInputBlur, this);
  this.inputFieldEl.onclick = searchAsYouTypeBind(this.handleInputClick, this);
  this.inputFieldEl.onmousedown = 
    searchAsYouTypeBind(this.handleInputMouseDown, this);

  this.inputFieldEl.setAttribute('autocomplete', 'off');

  if (window.addEventListener) { // Mozilla, Netscape, Firefox
    document.body.addEventListener('click', 
      searchAsYouTypeBind(this.handleBodyClick, this), false);
    document.addEventListener('keyup', 
      searchAsYouTypeBind(this.handleBodyKeyUp, this), false);
    document.addEventListener('keydown', 
      searchAsYouTypeBind(this.handleBodyKeyDown, this), false);
    document.addEventListener('keypress', 
      searchAsYouTypeBind(this.handleBodyKeyPress, this), false);
    window.addEventListener('resize', 
      searchAsYouTypeBind(this.handleBodyResize, this), false);
  } else { // IE
    document.body.attachEvent('onclick', 
      searchAsYouTypeBind(this.handleBodyClick, this));
    document.body.attachEvent('onkeyup', 
      searchAsYouTypeBind(this.handleBodyKeyUp, this));
    document.body.attachEvent('onkeydown', 
      searchAsYouTypeBind(this.handleBodyKeyDown, this));
    document.onkeypress = searchAsYouTypeBind(this.handleBodyKeyPress, this);
    window.attachEvent('onresize', 
      searchAsYouTypeBind(this.handleBodyResize, this));
  }

  // The below is for Firefox 1.5's fastback feature.
  // (...) CHANGE TO event listener
  try {
    window.onpageshow = function(event) { 
      if (event.persisted) {
        searchAsYouType.restoreInputField_(); 
      }
    };
  } catch(e) {
  }

  if ((this.browserFirefox) && (!this.browserFirefox10)) {
    window.onpagehide = searchAsYouTypeBind(this.saveInputField, this);
  } else {
    window.onunload = searchAsYouTypeBind(this.saveInputField, this);
  }
}

/**
 * Prepare a random tip for 5% of the queries. This tip will be shown as
 * the last search result.
 */
SearchAsYouType.prototype.prepareRandomTip_ = function() {
  var tips = [
    'You can use arrow keys to navigate these results.',
    'Press Tab, space or right arrow to auto-complete.',
    'Press Esc or up arrow to hide this pop-up. ' +
      'Press Esc again to quickly clear the search field.',
    'Click outside this pop-up to hide it. ' +
      'Click on the search bar twice to show it again.'];

  if (Math.random() < 0.05) {
    this.tipText = tips[Math.floor(Math.random() * tips.length)];
  } else {
    this.tipText = '';
  }
}

/**
 * Calculate and update the dimensions of Search-as-you-type elements,
 * including autocomplete, loading animation and shadows
 * @param {element} searchResultsEl A search results element to be updated
 */
SearchAsYouType.prototype.updateDimensionsAndShadow_ = 
  function(searchResultsEl) {
  // Figure out the absolute position of the input field element
  var el = this.inputFieldEl;
  var x = 0;
  var y = 0;
  var obj = el;
  do {
    x += obj.offsetLeft;
    y += obj.offsetTop;
    obj = obj.offsetParent;
  } while (obj);

  // Position the waiting animation, so it's inside the input field, flushed
  // right
  // (...) height too
  //this.waitingForSearchResultsEl.style.left = 
  //  (x + this.inputFieldEl.offsetWidth - 19) + 'px';
  //this.waitingForSearchResultsEl.style.top = 
  //  (y + 3) + 'px';

  // Position the autocomplete element
  this.autocompleteEl.setAttribute("originalLeft", x);
  this.autocompleteEl.style.top = y + 'px';
  this.autocompleteEl.style.height = 
    (this.inputFieldEl.clientHeight - 1) + 'px';

  // Position the search results canvas element
  if (searchResultsEl) {
    y += el.offsetHeight - 2;

    var w = el.offsetWidth - 2;

    searchResultsEl.style.left = (x + 1) + "px";
    searchResultsEl.style.top = y + "px";
    searchResultsEl.style.width = w + "px";

    x = searchResultsEl.offsetLeft;
    y = searchResultsEl.offsetTop;
    w = searchResultsEl.offsetWidth;
    var ch = searchResultsEl.scrollHeight;

    if (self.innerHeight) {
      var screenHeight = self.innerHeight;
    } else if (document.documentElement && 
               document.documentElement.clientHeight) { 
      var screenHeight = document.documentElement.clientHeight;
    } else if (document.body) {
      var screenHeight = document.body.clientHeight;
    }

    if (document.documentElement.scrollTop) {
      var scrollTop = document.documentElement.scrollTop;
    } else {
      var scrollTop = document.body.scrollTop;
    }

    var documentContentHeight = screenHeight - scrollTop; 
    
    var maxSearchResultsHeight = 
      documentContentHeight - y - searchAsYouTypeConfiguration.bottomPageMargin;

    if (ch > maxSearchResultsHeight) {
      searchResultsEl.style.height = maxSearchResultsHeight + "px";
    } else {
      searchResultsEl.style.height = "auto";
    }

	searchResultsEl.style.height = "auto";
	
    var h = searchResultsEl.offsetHeight;

    // Resize shadows
    this.resizeShadowEl_("", x, y, w + 4, h + 6);
    this.resizeShadowEl_("L", -2, 5, 2, h - 5);
    this.resizeShadowEl_("TL", -2, 0, 2, 5);
    this.resizeShadowEl_("TR", w, 0, 2, 5);
    this.resizeShadowEl_("R", w, 5, 2, h - 5);
    this.resizeShadowEl_("B", 4, h, w - 8, 6);
    this.resizeShadowEl_("BL", -2, h, 6, 6);
    this.resizeShadowEl_("BR", w - 4, h, 6, 6);
  }
}

/**
 * Resize one of the shadow elements.
 * @param {string} id Id of the shadow element (cf. "BR")
 * @param {int} x Horizontal position (in pixels)
 * @param {int} y Vertical position (in pixels)
 * @param {int} w Width (in pixels)
 * @param {int} h Height (in pixels)
 */
SearchAsYouType.prototype.resizeShadowEl_ = function(id, x, y, w, h) {
  var el = document.getElementById('searchAsYouTypeResultsShadow' + id);

  /* Wrapped around in try/catch because of an IE7 bug */
  try {
    el.style.left = x + "px";
    el.style.top = y + "px";
    el.style.width = w + "px";
    el.style.height = h + "px";
  } catch(e) {
  }
}

/**
 * Perform query search (an Ajax request) on whatever the user typed.
 * Skip if already in cache.
 */
SearchAsYouType.prototype.search_ = function(dontDelayShowResults) {
  if (dontDelayShowResults === true) {
    this.delayShowResults = false;
  } else {
    this.delayShowResults = true;
  }

  // If a query is empty we don't do anything
  if (this.typedQuery.length == 0) {
    this.changeWaitingForSearchResults_(false);
    return;
  }

  URL = searchAsYouTypeConfiguration.ajaxResponderUrl;
  URL += "?query=" + encodeURIComponent(this.typedQuery);
  URL += "&jsonp=searchAsYouType.handleAjaxResponse";
  if (this.debugMode) {
    URL += "&debug=1";
  }

  if (this.waitingForSearchResults) {
    this.cancelCurrentSearch_();
  }

  if (this.debugMode) {
    this.addToDebugConsoleTimesNewLine_("<td>" + this.typedQuery + "</td>");

    var date = new Date();
    this.debugQueryStartTime = date.getTime();
  }

  this.changeWaitingForSearchResults_(true);

  // If already in cache, use cache
  if (this.searchCache["_" + this.typedQuery]) { 
    this.ajaxRequestStartTime = -1;
    this.processResults_(this.searchCache["_" + this.typedQuery].results, true);
  } else {
    var date = new Date();
    this.ajaxRequestStartTime = date.getTime();

    this.ajaxObject = document.createElement('script');
    this.ajaxObject.src = URL;
    this.ajaxObject.type = "text/javascript";
    this.ajaxObject.charset = "utf-8";
    document.getElementsByTagName('head').item(0).appendChild(this.ajaxObject);
  }
}

/**
 * Cancel the Ajax request we're currently waiting for.
 */
SearchAsYouType.prototype.cancelCurrentSearch_ = function() {
  if (this.ajaxObject) {
    try {
      document.getElementsByTagName('head').item(0).
        removeChild(this.ajaxObject);
    } catch(e) {
    }
  }
}

/**
 * Show or hide the "results coming up" pie animation depending on 
 * whether it's needed. Abort the current Ajax request if necessary.
 * @param {bool} value Whether we're waiting or not for search results
 */
SearchAsYouType.prototype.changeWaitingForSearchResults_ = function(value) {
  if (this.waitingForSearchResults != value) {
    if (value) {
      //this.waitingForSearchResultsEl.style.visibility = 'visible';
    } else {
      //this.waitingForSearchResultsEl.style.visibility = 'hidden';

      this.cancelCurrentSearch_();
    }
  }
  
  this.waitingForSearchResults = value;
}

/**
 * Handle Ajax response when it's back. Add a tip if necessary, then forward
 * for processing.
 * @param {object} results Results object
 */
SearchAsYouType.prototype.handleAjaxResponse = function(results) {
  if (results.results.length && this.tipText) { 
    var moreDetailsUrl = searchAsYouTypeConfiguration.helpPageUrl;

    var content = '<p> ' + this.tipText;
    if (moreDetailsUrl) {
      content += ' <a' +
                 ' unselectable="on" class="unselectable moreDetails"' +
                 ' href="' + moreDetailsUrl + '">Learn more</a>';
    }
    content += '</p>';

    results.results.push({"type": "Tip", 
                          "name": "", 
                          "content": content, 
                          "style": "compact",
                          "moreDetailsUrl": moreDetailsUrl});
  }

  this.processResults_(results, false);
  
}

/**
 * Cache and process search results (Ajax response) if there are any.
 * @param {object} results Results object
 * @param {bool} cached Whether the results come from the cache
 */
SearchAsYouType.prototype.processResults_ = function(results, cached) {
  if (this.lastKeyPressed == 8) {
    var dontDoAutocomplete = true;
  } else {
    var dontDoAutocomplete = false;
  }

  if (!results.autocompletedQuery) {
    results.autocompletedQuery = results.query;
  }

  results.countNotCompact = 0;
  results.countExpanded = 0;
  for (var i in results.results) {
    if (results.results[i].style == 'expanded') {
      results.countExpanded++;
      results.countNotCompact++;
    } else if (results.results[i].style == 'normal') {
      results.countNotCompact++;
    }
  }

  // Copy the object for future reference
  this.results = searchAsYouTypeCloneObject(results);

  // Cache the results
  this.searchCache["_" + this.results.query] = {};
  this.searchCache["_" + this.results.query].results = 
    searchAsYouTypeCloneObject(results);

  this.resultsWindowHiddenByClicking = false;
  // See if the results respond to the last typed query (Ajax requests might 
  // come out of order)
  if (results.query == this.typedQuery) {

    // Add to debug info if we're in debug mode
    if (this.debugMode) {
      var date = new Date();
      var debugQueryEndTime = date.getTime();

      this.addToDebugConsoleTimesCurrentLine_(
        "<td>" + results.autocompletedQuery + "</td>");
      this.addToDebugConsoleTimesCurrentLine_(
        "<td>" + results.results.length + "</td>");
      this.addToDebugConsoleTimesCurrentLine_(
        "<td>" + searchAsYouTypeConfiguration.showResultsDelay + " ms</td>");
      if (cached) {
        this.addToDebugConsoleTimesCurrentLine_(
          "<td colspan='4'>(from cache)</td>");
      } else {
        this.addToDebugConsoleTimesCurrentLine_(
          "<td class='no'>" + (debugQueryEndTime - this.debugQueryStartTime) + 
          " ms</td>");
        this.addToDebugConsoleTimesCurrentLine_(
          "<td class='no'>" + this.results.debugInfo.globalTime + " ms</td>");
      }
    }

    if ((cached) && (dontDoAutocomplete)) {
      if (this.searchCache["_" + this.results.query].autocompleted) {
        this.hideResultsWindow_();
        this.changeWaitingForSearchResults_(false);
        return;
      }
    }

	
    // If nothing has been returned, hide the results window
    if (!this.results.results.length) {
  
      this.hideResultsWindow_();
      this.changeWaitingForSearchResults_(false);
    } else {
      this.prepareResultsWindow_();

      if (!dontDoAutocomplete) {
        this.addAutocompleteTextIfPossible_(); 
      }
    }
  }
}

/**
 * Get an HTML snippet showing the current result type. This is used if
 * we show summarized results.
 * @param {string} type Search result type (e.g. "Conference rooms")
 * @return {string} Corresponding HTML snippet
 */
SearchAsYouType.prototype.getResultTypeDescriptionHtml_ = function(type) {
  return '<h1>' + type + ": " + "</h1>";
}

/**
 * Get a CSS class name corresponding to a result type. What this does is
 * removes all of the spaces.
 * @param {string} type Search result type (e.g. "Conference rooms")
 * @return {string} Corresponding class name (e.g. "Conferencerooms")
 */
SearchAsYouType.prototype.getResultTypeClassName_ = function(type) {
  return type.replace(/\ /g, "");
}

/**
 * Get HTML markup for the results. 
 * @param {int} resultId Specific Search result to return (-1 if all)
 * @return {string} HTML markup for the result(s)
 */

SearchAsYouType.prototype.odst_select = function(x)
{
this.inputFieldEl.value = x;
} 
 
SearchAsYouType.prototype.getResultsHtml_ = function(resultId) {
 
  var html = '';
 

    for (var i = 0; i < this.results.results.length; i++) {
      
			 html += '<li onclick="searchAsYouType.odst_select(\'' + this.results.results[i].Display + '\')" class="' + this.results.results[i].Class + '">' +
							   this.results.results[i].Display +
							  '</li>';
			}				  

  return html;
}

/**
 * Prepare HTML markup for the search results window.
 */
SearchAsYouType.prototype.prepareResultsWindow_ = function() {
  var showExpanded;

  this.activeResult = -1;

  if (this.results.countNotCompact <= 
      searchAsYouTypeConfiguration.maxFullResults) {
    for (var i = 0; i < this.results.results.length; i++) {
      if (this.results.results[i].style == 'expanded') {
        this.results.results[i].style = 'expandedPriority';
      } else if (this.results.results[i].style == 'normal') {
        this.results.results[i].style = 'expanded';
      }
    }
  }

  this.resultsHtml = this.getResultsHtml_(-1);


  if (this.showResultsTimeoutId > -1) {
    clearTimeout(this.showResultsTimeoutId);
  }

  var time;

  if (this.delayShowResults) {
    if (this.ajaxRequestStartTime == -1) {
      time = 0;
    } else {
      var date = new Date();
      time = date.getTime() - this.ajaxRequestStartTime;
    }

    var time = searchAsYouTypeConfiguration.showResultsDelay - time;
    if (time <= 1) {
      time = 1;
    }
  } else {
    time = 1;
  } 

  this.showResultsTimeoutId =  
    setTimeout(searchAsYouTypeBind(this.showResultsWindow_, this), time);
}

/**
 * Show the search result window, incl. the shadow.
 */
SearchAsYouType.prototype.showResultsWindow_ = function() {
  this.showResultsTimeoutId = -1;

  this.changeWaitingForSearchResults_(false);
  clearInterval(this.hideTimeout);

  this.resultsWindowHiddenByClicking = false;
  this.resultsWindowHidden = false;

  // cleaning ids for safari
  var i = 0;
  var el;
  while (el = document.getElementById('searchResult' + i)) {
    el.id = '';           
    i++;
  }

  this.alternateSearchResultsEl.style.height = '1px';
  this.alternateSearchResultsEl.style.visibility = 'hidden';
  this.alternateSearchResultsEl.style.display = 'block';
  this.alternateSearchResultsEl.innerHTML = this.resultsHtml;
  this.alternateSearchResultsEl.style.opacity = 0.99;

  // We go through all of the links in the results, and remove tabindex
  // and make them override an iframe, if we're in one
  var els = this.alternateSearchResultsEl.getElementsByTagName('a');
  for (var i = 0, j = els.length; i < j; i++) {
    els.item(i).tabIndex = -1;
    els.item(i).target = "_top";
  }  

  // We go through all of the http://s.odst.co.uk/api/hilton/images, hide them, and assign the function
  // to show them when they're fully loaded. Since an image can resize
  // a search result window, we need to make sure that we recalculate the
  // dimensions (and shadows) on image load
  var els = this.alternateSearchResultsEl.getElementsByTagName('img');
  for (var i = 0, j = els.length; i < j; i++) {
    els.item(i).style.display = 'none';
    els.item(i).onload = 
      searchAsYouTypeBind(this.handleImageOnLoad, this, els.item(i));
  }

  this.updateDimensionsAndShadow_(this.alternateSearchResultsEl);

  this.searchResultsEl.style.visibility = 'hidden';
  this.searchResultsEl.style.display = 'none';

  this.searchResultsShadowEl.style.display = 'block';
  this.searchResultsShadowEl.style.visibility = 'visible';
  this.searchResultsShadowEl.style.opacity = 1;
  this.alternateSearchResultsEl.style.visibility = 'visible';

  // Swap search result elements handlers
  var el = this.searchResultsEl;
  this.searchResultsEl = this.alternateSearchResultsEl;
  this.alternateSearchResultsEl = el;
}

/**
 * Show the image after it's loaded. Prevents http://s.odst.co.uk/api/hilton/images loading and layout
 * reflowing bit by bit -- it only shows the image if it is fully loaded.
 *
 * @param {element} el The image to be shown
 */
SearchAsYouType.prototype.handleImageOnLoad = function(el) {
  if (el) {
    el.style.display = 'inline';

    this.updateDimensionsAndShadow_(this.searchResultsEl);
  }

  return false;
}

/**
 * Hide the search results window. This initializes the fadeout.
 */
SearchAsYouType.prototype.hideResultsWindow_ = function() {
  if (this.resultsWindowHidden) {
    return;
  }

  this.clearAutocomplete_(true);

  this.hideOpacity = this.searchResultsEl.style.opacity;
  clearInterval(this.hideTimeout);
  this.fadeLastTime = new Date().getTime();
  this.hideTimeout = 
    setInterval(searchAsYouTypeBind(this.fadeResultsWindow_, this), 20);

  this.resultsWindowHidden = true;
  this.activeResult = -1;
}

/**
 * Fade the search results window a little bit more. We're counting the 
 * time so it should always take the same amount of time, only perhaps be a 
 * little less smooth on less powerful machines.
 */
SearchAsYouType.prototype.fadeResultsWindow_ = function() {
  var newTime = new Date().getTime();

  this.hideOpacity -= (newTime - this.fadeLastTime) * 0.005;
  this.fadeLastTime = newTime;

  if (this.hideOpacity <= 0) {
    clearInterval(this.hideTimeout);
    this.searchResultsEl.style.display = 'none';
    this.searchResultsShadowEl.style.visibility = 'hidden';
  } else {
    this.searchResultsEl.style.opacity = this.hideOpacity;
    this.searchResultsShadowEl.style.opacity = this.hideOpacity;
  }
}

/**
 * Activate (highlight) a result. Used for keyboard navigation
 * between search results.
 * @param {int} no The number of the result to activate
 */
SearchAsYouType.prototype.highlightSearchResult_ = function(no) {
  document.getElementById('searchResult' + no).className += " highlighted";
}

/**
 * Deactivate (de-highlight) a result. Used for keyboard navigation
 * between search results.
 * @param {int} no The number of the result to deactivate
 */
SearchAsYouType.prototype.unhighlightSearchResult_ = function(no) {
  document.getElementById('searchResult' + no).className =
    document.getElementById('searchResult' + no).className.
    replace(/ highlighted/, "");
}

/**
 * Activate (highlight) a next result, if possible.
 */
SearchAsYouType.prototype.highlightNextSearchResult_ = function() {
  if (this.results.results.length) {
    if (this.activeResult == -1) {
      this.activeResult = 0;
      if (this.inputFieldHasFocus) {
        this.inputFieldEl.blur();
      }
      this.highlightSearchResult_(this.activeResult);
    } else if (this.activeResult < this.results.results.length - 1) {
      this.unhighlightSearchResult_(this.activeResult);
      this.activeResult++;
      this.highlightSearchResult_(this.activeResult);
    }
  }
}

/**
 * Deactivate (de-highlight) a next result, if possible.
 */
SearchAsYouType.prototype.highlightPrevSearchResult_ = function() {
  if (this.results.results.length) {
    if (this.activeResult == 0) {
      // Going up from the first result will get us back in the input field
      this.unhighlightSearchResult_(this.activeResult);
      this.activeResult = -1;
      this.inputFieldEl.focus();
    } else if (this.activeResult > 0) {
      this.unhighlightSearchResult_(this.activeResult);
      this.activeResult--;
      this.highlightSearchResult_(this.activeResult);
    }
  }
}

/**
 * Expand a summarized result.
 * @param {event} e Browser event (or null if invoked from here)
 * @param {int} id Id of the result to be expanded
 */
SearchAsYouType.prototype.expandSummaryResult_ = function(e, id) {
  e = e || window.event;

  if (e) {
    e.cancelBubble = true;
  }

  var dividerEl = document.createElement("divider");
  var el = document.getElementById('searchResult' + id);
  var result = this.results.results[el.getAttribute('originalId')];
  var elParent = el.parentNode;

  elParent.insertBefore(dividerEl, el);
  elParent.removeChild(el);

  elParent.parentNode.innerHTML = 
    elParent.parentNode.innerHTML.replace(/<divider>/, 
      "</div>" + this.getResultsHtml_(id) + 
      "<div class='searchResult summary " + 
      this.getResultTypeClassName_(result.type) + "'>");

  var el = document.getElementById('searchResult' + id);

  var newEl = document.createElement("span");
  newEl.innerHTML = '&nbsp;&middot; ';

  var elPrev = el.previousSibling;

  if (elPrev) {
    if (!elPrev.getElementsByTagName('a').length) {
      elPrev.parentNode.removeChild(elPrev);
    } else {
      elPrev.innerHTML = 
        elPrev.innerHTML.replace(new RegExp(newEl.innerHTML + "$"), "");
    }
  }

  var elNext = el.nextSibling;
  if (elNext) {
    if (!elNext.getElementsByTagName('a').length) {
      elNext.parentNode.removeChild(elNext);
    } else {
      elNext.innerHTML = 
        elNext.innerHTML.replace(new RegExp("^" + newEl.innerHTML), 
        this.getResultTypeDescriptionHtml_(result.type));
    }
  }

  this.updateDimensionsAndShadow_(this.searchResultsEl);

  return false;
}

/**
 * Add autocomplete if it's available.
 * @return {boolean} true if added, false if not
 */
SearchAsYouType.prototype.addAutocompleteTextIfPossible_ = function() {
  var results = this.results;

  if (!results.query) {
    return; // not there yet
  }

  var inputFieldValue = this.getInputFieldValue_().toLowerCase();

  if ((results.query.toLowerCase() == 
       inputFieldValue.substr(0, results.query.length)) &&
      (inputFieldValue == 
       results.autocompletedQuery.substr(0, inputFieldValue.length).
         toLowerCase())) {
    this.autocomplete = 
      results.autocompletedQuery.substring(inputFieldValue.length);

    if (this.autocomplete) {
      var noAutocomplete = this.inputFieldEl.value.replace(/\ /, "&nbsp;");

      this.autocompleteHelperEl.style.display = 'block';
      this.autocompleteHelperEl.innerHTML = noAutocomplete;
      var noAutocompleteWidth = this.autocompleteHelperEl.offsetWidth;
      this.autocompleteHelperEl.innerHTML = this.autocomplete;
      var autocompleteWidth = this.autocompleteHelperEl.offsetWidth;
      this.autocompleteHelperEl.style.display = 'none';

      this.autocompleteEl.innerHTML = 
        this.autocomplete.replace(/\ /, "&nbsp;");
      this.autocompleteEl.style.left = 
        (parseInt(this.autocompleteEl.getAttribute("originalLeft")) + 
        noAutocompleteWidth) + "px";

      this.autocompleteEl.style.display = 'block';
    } else {
      this.autocompleteEl.style.display = 'none';
    }
    return true;
  }
  this.clearAutocomplete_(true);
  return false;
}

/**
 * Collapse autocomplete, i.e. make it part of the actual input field.
 */
SearchAsYouType.prototype.collapseAutocomplete_ = function() {
  if (this.autocomplete) {
    this.inputFieldEl.value += this.autocomplete + " ";
    this.inputFieldEl.selectionStart = this.inputFieldEl.value.length;
    this.inputFieldEl.selectionEnd = this.inputFieldEl.value.length;
    this.clearAutocomplete_(false);
  }
}

/**
 * Clear and hide autocomplete if present.
 * @param {boolean} hideResultsWindow Whether to hide the results window after
 *                                    clearing autocomplete
 */
SearchAsYouType.prototype.clearAutocomplete_ = function(hideResultsWindow) {
  if (this.autocomplete != '') {
    this.autocomplete = '';
    this.autocompleteEl.innerHTML = '';
    this.autocompleteEl.style.display = 'none';
    if (hideResultsWindow) {
      this.hideResultsWindow_();
    }
  }
}

/**
 * Handle a key press event in the input field.
 * @param {event} e Browser event
 */      
SearchAsYouType.prototype.handleInputKeyPress = function(e) {
  if (!this.initialized) { 
    return;
  }
  var valueToReturn = true;

  e = e || window.event;
  var whichKey = (e.which) ? e.which : e.keyCode;

  switch (whichKey) {
    case 9: // Tab
      if (this.autocompleteJustCollapsed) {
        valueToReturn = false;
      }
      break;
  }

  return valueToReturn;
}

/**
 * Handle a key down event in the input field.
 * @param {event} e Browser event
 */      
SearchAsYouType.prototype.handleInputKeyDown = function(e) {
  if (!this.initialized) {
    return;
  }

  e = e || window.event;
  var whichKey = (e.which) ? e.which : e.keyCode;

  if ((whichKey == 8) || (whichKey == 46)) {
    this.clearAutocomplete_(false);
  } 
}

/**
 * Handle a key up event in the input field. Fire a search query if
 * applicable.
 * @param {event} e Browser event
 */      
SearchAsYouType.prototype.handleInputKeyUp = function(e) {
  if (!this.initialized) return;

  e = e || window.event;
  var whichKey = (e.which) ? e.which : e.keyCode;

  this.lastKeyPressed = whichKey;

  if (this.autocompleteJustCollapsed) {
    this.typedQuery = this.lastTypedQuery = this.getInputFieldValue_();
    this.autocompleteJustCollapsed = false;
    return;
  }

  // Changing the query to lowercase and stripping out the white
  // space surrounding it
  var query = this.getInputFieldValue_();

  if (query != this.typedQuery) {
    if (this.showResultsTimeoutId > -1) {
      clearTimeout(this.showResultsTimeoutId);
    }

    this.lastTypedQuery = this.typedQuery;

    // We don't auto-complete on Backspace
    if (whichKey != 8) {
      if (this.addAutocompleteTextIfPossible_()) {
        this.typedQuery = this.lastTypedQuery = this.getInputFieldValue_();

        this.search_();
      }
    }

    this.typedQuery = this.getInputFieldValue_();

    if (this.lastTypedQuery != this.typedQuery) {
      if (this.keystrokeTimeoutId != -1) {
        clearTimeout(this.keystrokeTimeoutId);
        this.keystrokeTimeoutId = -1;
      }
      if (!this.typedQuery) {
        this.hideResultsWindow_();
        this.clearInputField_();
      }

      if (whichKey == 8) {
        this.clearAutocomplete_(true);
      }

      this.keystrokeTimeoutId = setTimeout(
                         searchAsYouTypeBind(this.search_, this), 
                         searchAsYouTypeConfiguration.keystrokeDelay);
    }
  }

  return true;
}

/**
 * Handle a key down event in the document body.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleBodyKeyDown = function(e) {
  var valueToReturn = true;

  if (!this.initialized) {
    return;
  }

  e = e || window.event;
  var whichKey = (e.which) ? e.which : e.keyCode;
  var targetElement = (e.target) ? e.target : e.srcElement;

  switch (whichKey) {
    case 13: // Enter
    case 32: // space
      if ((!this.resultsWindowHidden) && (this.activeResult >= 0)) {
        if (document.getElementById('searchResult' + this.activeResult).
              className.indexOf('summarized') == -1) {
          // Pressing Enter or space while a search result is active (navigated
          // to from the keyboard) will follow the "More info" link
          var el = document.getElementById('searchResult' + this.activeResult);

          if (el.href) {
            var url = el.href;
          } else if (el.getAttribute("moreDetailsUrl")) { 
            var url = el.getAttribute("moreDetailsUrl");
          }

          if (url) {
            this.hideResultsWindow_();
            this.goToUrl_(url);
          } 
        } else {
          // Otherwise zoom in on a given summary record.
          this.expandSummaryResult_(null, this.activeResult);
          this.highlightSearchResult_(this.activeResult);
        }
        valueToReturn = false;
      } 
      break;

    case 27: // Escape
      // Escape can do three things, in order of precedence:
      // 1. If the page with results is loading, Escape should
      //    be handled by the browser to cancel loading the page.
      // 2. If the pop-down with results is shown, Escape should
      //    remove it.
      // 3. Otherwise it should clear the field.

      if (this.inputFieldHasFocus) {
        // Safari sends Esc code twice, so we ignore the second time
        // it happens
        if (this.browserSafari && !this.browserSafari3OrHigher) {
          if (this.escapeKeyJustPressed) {
            this.escapeKeyJustPressed = false;
            break; 
          } else {
            this.escapeKeyJustPressed = true;
          }
        }

        if (!this.resultsWindowHidden) { 
          this.hideResultsWindow_();
          valueToReturn = false;
          this.inputFieldEl.focus();
          this.inputFieldHasFocus = 1;
        } else {
          this.clearInputField_();
          valueToReturn = false;
        }
      }
      break;

    case 35: // End
      if ((this.inputFieldHasFocus) && (this.autocomplete != '')) {
        this.collapseAutocomplete_();
        this.autocompleteJustCollapsed = true;
      }
      break;

    case 40: // down arrow
    case 63233: // down arrow
    case 39: // right arrow
      if (whichKey == 39) {
        if ((this.inputFieldHasFocus) && (this.autocomplete != '')) {
          this.collapseAutocomplete_();
          this.autocompleteJustCollapsed = true;
        }
      }

      // If we press down arrow in the input field, we can force the 
      // re-query 
      if ((this.resultsWindowHidden) && (this.inputFieldHasFocus) && 
          (whichKey != 39)) {
        this.search_(true);
        valueToReturn = false;
      } else if ((!this.resultsWindowHidden) && 
                 ((this.activeResult >= 0) || 
                  ((whichKey != 39) && (this.inputFieldHasFocus)))) {
      // If not, right or down arrow activate the next result
        this.highlightNextSearchResult_();
        valueToReturn = false;
        this.arrowKeyProcessed = true;
      }

      break;

    case 38: // up arrow
    case 63235: // up arrow
    case 37: // left arrow
      if (whichKey == 37) {
        this.clearAutocomplete_(true);
      }

      // If we press up arrow in the input field, we hide the pop-down
      if ((!this.resultsWindowHidden) && (this.inputFieldHasFocus) && 
          (whichKey != 37)) {
        this.hideResultsWindow_();
        valueToReturn = false;
        this.arrowKeyProcessed = true;
      } else if ((!this.resultsWindowHidden) && (this.activeResult >= 0)) {
        // If not, left or up arrow activate the previous result
        this.highlightPrevSearchResult_();
        valueToReturn = false;
        this.arrowKeyProcessed = true;
      }
      break;

    case 9: // Tab
      if (this.inputFieldHasFocus && (this.autocomplete != '')) {
        this.collapseAutocomplete_();
        this.autocompleteJustCollapsed = true;
        valueToReturn = false;
      }
      break;
  }

  if (!this.resultsWindowHidden && valueToReturn) {
    if (((!this.inputFieldHasFocus) && ((whichKey < 37) || (whichKey > 40))) ||
        ((whichKey == 9) && (!this.autocompleteJustCollapsed))) {
      this.hideResultsWindow_();
    }
  }

  if (!valueToReturn) {
    e.returnValue = false;
    if (e.preventDefault) {
      e.preventDefault();
    }
  }
}

/**
 * Handle a key press event in the document body.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleBodyKeyPress = function(e) {
  var valueToReturn = true;

  if (this.initialized) {
    e = e || window.event;
    var whichKey = (e.which) ? e.which : e.keyCode;

    // Arrow keys have the same key codes here as some other characters
    // (for example, down arrow is the same as left parenthesis)
    // We have to detect whether the arrow key was pressed during key down,
    // and then ignore it here if that's the case (otherwise it'd scroll
    // the screen)
    if ((this.arrowKeyProcessed) && (whichKey >= 37) && (whichKey <= 40)) {
      this.arrowKeyProcessed = false;
      valueToReturn = false;
    }

    if (!valueToReturn) {
      e.returnValue = false;
      if (e.preventDefault) {
        e.preventDefault();
      }
    }
  }
}

/**
 * Handle a key up event in the document body.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleBodyKeyUp = function(e) {
  var valueToReturn = true;
  
  e = e || window.event;
  var whichKey = (e.which) ? e.which : e.keyCode;
  var targetElement = (e.target) ? e.target : e.srcElement;

  this.arrowKeyProcessed = false;

  switch (whichKey) {
    case 32: // space
      if (this.inputFieldHasFocus && (this.autocomplete != '')) {
        this.clearAutocomplete_(true);
        valueToReturn = false;
      }
      break;
  }

  if (!valueToReturn) {
    e.returnValue = false;
    if (e.preventDefault) {
      e.preventDefault();
    }
  }
}    

/**
 * Handle a resize event in the document body (to recalculate the search
 * results window and its shadow).
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleBodyResize = function(e) {
  this.updateDimensionsAndShadow_(this.searchResultsEl);
}    

/**
 * Handle input field losing focus. Remember this in a variable.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleInputBlur = function(e) {
  this.inputFieldHasFocus = 0;
}

/**
 * Handle input field receiving focus. Remember this in a variable.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleInputFocus = function(e) {
  this.inputFieldHasFocus = 0.5;
}

/**
 * Handle mouse down on the input field. Collapses autocomplete if 
 * present.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleInputMouseDown = function(e) {
  if (this.autocomplete) {
    this.collapseAutocomplete_();
  }
}

/**
 * Handle mouse down on an autocomplete object. Collapses autocomplete if 
 * present.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleAutocompleteMouseDown = function(e) {
  if (this.autocomplete) {
    this.collapseAutocomplete_();
  }
}

/**
 * Handle input field receiving a click.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleInputClick = function(e) {
  e = e || window.event;
  e.cancelBubble = true;

  // Clicking on the input field again when it's already active
  // shows the pop-down again
  if (this.inputFieldHasFocus == 1) {
    if (this.resultsWindowHidden) {
      this.search_(true);
    }
  } else {
    this.inputFieldHasFocus = 1;
  }
}

/**
 * Handle a click on a search result. Goes to a "more details" URL if the
 * given search result has any.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleSearchResultClick = function(e) {
  e = e || window.event;
  var el = (e.target) ? e.target : e.srcElement;

  while ((el.tagName != 'DIV') ||
         (el.className.indexOf('searchResult') == -1)) {
    el = el.parentNode;
  }

  if (el.getAttribute("moreDetailsUrl")) {
    this.goToUrl_(el.getAttribute("moreDetailsUrl"));
  }
}

/**
 * Handle a click in document body.
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.handleBodyClick = function(e) {
  e = e || window.event;
  var targetElement = (e.target) ? e.target : e.srcElement;

  this.clearAutocomplete_();
  this.hideResultsWindow_();
  this.resultsWindowHiddenByClicking = true;
}

/**
 * Go to a specific URL. If the current page is inside an iframe, it breaks
 * out of that iframe.
 * @param {string} url URL to go to
 */
SearchAsYouType.prototype.goToUrl_ = function (url) {
  try {
    if (top.location != location) {
      top.location.href = url;
    } else {
      location.href = url;
    }
  } catch(e) {
    location.href = url;
  }
} 

/**
 * Activate the debug mode, create the debug console.
 */
SearchAsYouType.prototype.activateDebugConsole_ = function() {
  document.write("<div onclick='event.cancelBubble = true;' " +
    "id='searchAsYouTypeDebugConsole' class='expanded'>" +
    "<div style='float: right'>" +
    "<button onclick='searchAsYouType.clearDebugConsoleTimes()'>Clear " +
    "console</button>" +
    "<button onclick='searchAsYouType.clearCache()'>Clear cache</button>" +
    "<button onclick='searchAsYouType.toggleDebugConsole(event)'>Show/hide" +
    "</button>" +
    "</div><h1>Search-as-you-type debug console</h1>" +
    "<br />" +
    "<table id='searchAsYouTypeDebugTimes'>" +
    "</table>" +
    "</div>");

 this.debugConsoleTimesHeader = 
    '<tr><th>Query</th>' +
    '<th>Auto-completed</th>' +
    '<th>No. of results</th>' +
    '<th>Delay before<br />displaying:<br />(fixed)</th>' +
    '<th title="JS: Time from launching a query to displaying it">' +
    'Total turn-around<br />client+server</th>' +
    '<th title="Ajax: Total time spent on the server">' +
    'Server:<br />Total time</th>' +
    '</tr>';

  this.clearDebugConsoleTimes();
}

/**
 * Show or hide the debug console.     
 * @param {event} e Browser event
 */
SearchAsYouType.prototype.toggleDebugConsole = function(e) {
  var debugConsoleEl = document.getElementById('searchAsYouTypeDebugConsole');

  if (debugConsoleEl.className.indexOf('expanded') != -1) {  
    debugConsoleEl.className = 
      debugConsoleEl.className.replace(/expanded/, 'contracted');
  } else {
    debugConsoleEl.className = 
      debugConsoleEl.className.replace(/contracted/, 'expanded');
  }

  e = e || window.event;
  e.cancelBubble = true;

  this.inputFieldEl.focus();
}

/**
 * Add a new line to a debug console times table.
 * @param {text} line A new line to be added
 */
SearchAsYouType.prototype.addToDebugConsoleTimesNewLine_ = function(line) {
  this.debugConsoleTimesContents = 
    this.debugConsoleTimesCurrentLine + this.debugConsoleTimesContents;

  this.debugConsoleTimesCurrentLine = "<tr>" + line;

  document.getElementById("searchAsYouTypeDebugTimes").innerHTML = 
    this.debugConsoleTimesHeader + this.debugConsoleTimesCurrentLine + 
    this.debugConsoleTimesContents;
}

/**
 * Append to the most recent line to a debug console times table.
 * @param {text} line A text to be appended
 */
SearchAsYouType.prototype.addToDebugConsoleTimesCurrentLine_ = function(line) {
  this.debugConsoleTimesCurrentLine += line;

  document.getElementById("searchAsYouTypeDebugTimes").innerHTML = 
    this.debugConsoleTimesHeader + this.debugConsoleTimesCurrentLine + 
    this.debugConsoleTimesContents;
}

/**
 * Clear the debug console.
 */
SearchAsYouType.prototype.clearDebugConsoleTimes = function() {
  this.debugConsoleTimesContents = '';
  this.debugConsoleTimesCurrentLine = '';
  document.getElementById("searchAsYouTypeDebugTimes").innerHTML = 
    this.debugConsoleTimesHeader;

  this.inputFieldEl.focus();
}

/**
 * Clear the search cache. Used only for debugging.
 */
SearchAsYouType.prototype.clearCache = function() {
  this.searchCache = [];

  this.inputFieldEl.focus();
}

/**
 * A helper function which partially applies a function to a particular 
 * "this" object and zero or more arguments. The result is a new function 
 * with some arguments of the first function pre-filled and the value 
 * of |this| "pre-specified".
 *
 * Remaining arguments specified at call-time are appended to the pre-
 * specified ones.
 */
function searchAsYouTypeBind(fn, self, var_args) {
  var boundargs = fn.boundArgs_ || [];
  boundargs = boundargs.concat(Array.prototype.slice.call(arguments, 2));

  if (typeof fn.boundSelf_ != "undefined") {
    self = fn.boundSelf_;
  }

  if (typeof fn.foundFn_ != "undefined") {
    fn = fn.boundFn_;
  }

  var newfn = function() {
    // Combine the static args and the new args into one big array
    var args = boundargs.concat(Array.prototype.slice.call(arguments));
    return fn.apply(self, args);
  }

  newfn.boundArgs_ = boundargs;
  newfn.boundSelf_ = self;
  newfn.boundFn_ = fn;

  return newfn;
}

/** 
 * A helper function cloning an object. It should support well arrays and
 * objects inside the object being cloned.
 * @param {object} obj An object to be cloned
 * @return {object} A cloned object
 */
function searchAsYouTypeCloneObject(obj) {
  if (obj instanceof Array) {
    var newObj = [];
  } else {
    var newObj = {};
  }

  for (var i in obj) {
    if (obj[i].constructor.toString().indexOf("Array") != -1) {
      newObj[i] = searchAsYouTypeCloneObject(obj[i]);
    } else if (typeof obj[i] == 'object') {
      newObj[i] = searchAsYouTypeCloneObject(obj[i]);
    } else {
      newObj[i] = obj[i];
    }
  }

  return newObj;
}

// Instantiating the object...
var searchAsYouType = new SearchAsYouType();

// If a callback function is defined, call it now. This compensates 
// for <script onload> not working in some browsers.
try {
  if (searchAsYouTypeCallback) {
    searchAsYouTypeCallback(); 
  }
} catch(e) {
}

<?php

$w = $_GET['w'];
$h = $_GET['h']; 
$id = $_GET['id'];
$affiliate_id = $_GET['aid'];
$campaign_id = $_GET['cid'];
?>

document.write('<link href="http://s.odst.co.uk/api/hilton/styles.css" rel="stylesheet" type="text/css" />');
document.write('<!--[if IE]>');
document.write('<link rel="stylesheet" type="text/css" href="http://s.odst.co.uk/api/hilton/ieStyles.css" />');
document.write('<![endif]--><link href="http://s.odst.co.uk/api/hilton/jquery-ui-1.8.23.custom.css" rel="stylesheet" type="text/css" />');
document.write('<link href="http://s.odst.co.uk/api/hilton/sayt.css" rel="stylesheet" type="text/css" />');
if (typeof jQuery == 'undefined') {  
  document.write('<script src="http://s.odst.co.uk/api/hilton/js/jquery-1.8.0.min.js" type="text/javascript" /></script>');
}
document.write('<script src="http://s.odst.co.uk/api/hilton/js/jquery-ui-1.8.23.custom.min.js" type="text/javascript"></script>');
document.write('<script src="http://s.odst.co.uk/api/hilton/js/odst_hilton.js" type="text/javascript"></script>');
document.write('');
document.write('<form id="frm_odst_hilton" method="POST" action="http://www.awin1.com/awclick.php?awinmid=3624&awinaffid=<?php echo($affiliate_id)?>&clickref=<?php echo($campaign_id)?>&p=http://www3.hilton.com/en_US/hi/search/findhotels/index.htm">');
document.write('	<input type="hidden" name="searchType" value="ALL">');
document.write('	<input type="hidden" name="searchQuery" value="">');
document.write('<input type="hidden" name="arrivalDate" value="">');
document.write('<input type="hidden" name="departureDate" value="">');
document.write('<input type="hidden" name="radiusFromLocation" value="40">');
document.write('<input type="hidden" name="radiusUnits" value="MILES">');
document.write('<input type="hidden" name="_flexibleDates" value="on">');
document.write('<input type="hidden" name="_rewardBooking" value="on">');
document.write('<input type="hidden" name="numberOfRooms" value="1">');
document.write('<input type="hidden" name="numberOfAdults[0]" value="1">');
document.write('<input type="hidden" name="numberOfChildren[0]" value="0">');
document.write('<input type="hidden" name="numberOfAdults[1]" value="1">');
document.write('<input type="hidden" name="numberOfChildren[1]" value="0">');
document.write('<input type="hidden" name="numberOfAdults[2]" value="1">');
document.write('<input type="hidden" name="numberOfChildren[2]" value="0">');
document.write('<input type="hidden" name="numberOfAdults[3]" value="1">');
document.write('<input type="hidden" name="numberOfChildren[3]" value="0">');
document.write('<input type="hidden" name="promoCode" value="">');
document.write('<input type="hidden" name="srpIds" value="">');
document.write('<input type="hidden" name="onlineValueRate" value="">');
document.write('<input type="hidden" name="groupCode" value="">');
document.write('<input type="hidden" name="corporateId" value="">');
document.write('<input type="hidden" name="_rememberCorporateId" value="on">');
document.write('<input type="hidden" name="_aaaRate" value="on">');
document.write('<input type="hidden" name="_aarpRate" value="on">');
document.write('<input type="hidden" name="_seniorRate" value="on">');
document.write('<input type="hidden" name="_governmentRate" value="on">');
document.write('<input type="hidden" name="_travelAgentRate" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="CH">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="DT">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="ES">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="HP">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="HI">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="GI">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="HT">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="HW">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="selectedHotelBrands" value="WA">');
document.write('<input type="hidden" name="_selectedHotelBrands" value="on">');
document.write('<input type="hidden" name="searchAllBrands" value="true">');
document.write('<input type="hidden" name="_searchAllBrands" value="on">');


<?php
if( $w == "160" && $h == "600" )
{
?>
document.write('	<table class="odst" width="160" height="600" border="0" cellpadding="8" cellspacing="0"  >');
document.write('		<tr>');
document.write('			<td align="left" valign="middle" background="http://s.odst.co.uk/api/hilton/images/160x600back.jpg" style="padding:10px">');
document.write('				<table class="odst"  border="0" table="table" width="140" height="250" cellspacing="0" cellpadding="0" class="vstyle" style="margin-top: 140px;">');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="city" style="margin-bottom:5px; display:inline-block;">WHERE ARE YOU GOING?</label>');
document.write('							<br />   ');
document.write('							<span class="odt_spanTextInput">');
document.write('								<input onfocus="searchAsYouType.initialize(this, false);" id="odst_locationTextInput" type="text" value="City, airport, address, attraction, or hotel" maxlength="100" autocomplete="off" style="width:125px;"/>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="guests">Guests</label>');
document.write('							<br />   ');
document.write('							<select name="guests"  style="margin-top:5px;">');
document.write('								<option value="1">1</option>');
document.write('								<option value="2">2</option>');
document.write('								<option value="3">3</option>');
document.write('								<option value="4">4 </option>');
document.write('                    	    </select>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="checkin" class="labelTop arrival" style="margin-bottom: 7px; display: inline-block;">Arrival</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="from" class="odst_from" name="arrivalDate" >');
document.write('								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td> ');
document.write('							<label for="checkout" class="labelTop departure" style="margin-bottom: 7px; display: inline-block;">Departure</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="to" class="odst_to" name="departureDate" >');
document.write('								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>');
document.write('							</span> ');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<a href="#" class="odst_findbutton" title="Find it" role="button" style="width: 41px; height: 11px;">');
document.write('								<span class="odst_text" >Find it</span>');
document.write('								<span class="arrow_icon">&nbsp;</span>');
document.write('							</a>');
document.write('						</td>');
document.write('					</tr>');
document.write('               	 </table>');
document.write('            </td>');
document.write('        </tr>');
document.write('	</table>');

<?php
 }
 else if( $w == "120" && $h == "600" )
{
?>

document.write('	<table class="odst" width="120" height="600" border="0" cellpadding="8" cellspacing="0"  >');
document.write('		<tr>');
document.write('			<td align="left" valign="middle" background="http://s.odst.co.uk/api/hilton/images/120x600back.jpg" style="padding:10px">');
document.write('				<table class="odst" border="0" table="table" width="100" height="270" cellspacing="0" cellpadding="0" class="vstyle" style="margin-top: 100px;">									<tr>');
document.write('						<td>');
document.write('							<label for="city" style="margin-bottom:5px; display:inline-block;">WHERE ARE YOU GOING?</label>');
document.write('							<br />   ');
document.write('							<span class="odt_spanTextInput">');
document.write('								<input onfocus="searchAsYouType.initialize(this, false);" id="odst_locationTextInput" type="text" value="City, airport, address, attraction, or hotel" maxlength="100" autocomplete="off" style="width:90px;"/>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="guests">Guests</label>');
document.write('							<br />   ');
document.write('							<select name="guests"  style="margin-top:5px;">');
document.write('								<option value="1">1</option>');
document.write('								<option value="2">2</option>');
document.write('								<option value="3">3</option>');
document.write('								<option value="4">4 </option>');
document.write('                    	    </select>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="checkin" class="labelTop arrival" style="margin-bottom: 7px; display: inline-block;">Arrival</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="from" class="odst_from" name="arrivalDate" style="width: 80px!important" >');
document.write('								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td> ');
document.write('							<label for="checkout" class="labelTop departure" style="margin-bottom: 7px; display: inline-block;">Departure</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="to" class="odst_to" name="departureDate" style="width: 80px!important" >');
document.write('								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>');
document.write('							</span> ');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<a href="#" class="odst_findbutton" title="Find it" role="button" style="margin-top:5px; width: 42px; height: 11px;">');
document.write('								<span class="odst_text" >Find it</span>');
document.write('								<span class="arrow_icon">&nbsp;</span>');
document.write('							</a>');
document.write('						</td>');
document.write('					</tr>');
document.write('');
document.write('						');
document.write('               	 </table>');
document.write('            </td>');
document.write('        </tr>');
document.write('	</table>');


<?php
 }
 else if( $w == "460" && $h == "125" )
{
?>

document.write('	<table class="odst" width="460" height="125" border="0" cellpadding="8" cellspacing="0"  >');
document.write('		<tr>');
document.write('			<td align="right" valign="middle" background="http://s.odst.co.uk/api/hilton/images/460x125back.jpg" style="padding:10px">');
document.write('				<table valign="top" class="odst" border="0" table="table" width="320" height="50" cellspacing="0" cellpadding="0" class="vstyle" style="text-align: left; margin-left: 127px!important;">');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="city" style="margin-bottom:5px; display:inline-block;">WHERE ARE YOU GOING?</label>');
document.write('							<br />   ');
document.write('							<span class="odt_spanTextInput">');
document.write('								<input onfocus="searchAsYouType.initialize(this, false);"  id="odst_locationTextInput" type="text" value="City, airport, address, attraction, or hotel" maxlength="100" autocomplete="off" style="width:320px;"/>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('				</table>');
document.write('				<table class="odst" valign="bottom" border="0" table="table" width="320" height="50" cellspacing="0" cellpadding="0" class="vstyle" style=" margin-left: 127px!important;" >');
document.write('					<tr>');
document.write('						<td align="left" width="50px">');
document.write('							<label for="guests"  style="margin-bottom: 5px;">Guests</label>');
document.write('							<br/>');
document.write('							<select name="guests" style="margin-top: 5px;">');
document.write('								<option value="1">1</option>');
document.write('								<option value="2">2</option>');
document.write('								<option value="3">3</option>');
document.write('								<option value="4">4 </option>');
document.write('                    	    </select>');
document.write('						</td>					');
document.write('						<td width="101px" align="left">');
document.write('							<label for="checkin" class="labelTop arrival" style="margin-bottom: 7px; display: inline-block;">Arrival</label>');
document.write('							<br/>');
document.write('													');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="from" class="odst_from" name="arrivalDate" >');
document.write('								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>');
document.write('							</span>');
document.write('						</td>');
document.write('						');
document.write('						<td width="101px" align="left"> ');
document.write('							<label for="checkout" class="labelTop departure" style="margin-bottom: 7px; display: inline-block;">Departure</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="to" class="odst_to" name="departureDate" >');
document.write('								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>');
document.write('							</span> ');
document.write('						</td>');
document.write('						<td align="right" style="vertical-align: middle;">');
document.write('							<a href="#" class="odst_findbutton" title="Find it" role="button" valign="bottom" style=" margin-left: 5px; width: 50px; height: 11px; ">');
document.write('								<span class="odst_text" >Find it</span>');
document.write('								');
document.write('							</a>');
document.write('						</td>');
document.write('					</tr>');
document.write('               	 </table>');
document.write('            </td>');
document.write('        </tr>');
document.write('	</table>');
<?php
}
 else if( $w == "728" && $h == "90" )
{
?>	

document.write('	<table class="odst" width="728" height="90" border="0" cellpadding="8" cellspacing="0"  >');
document.write('		<tr>');
document.write('			<td align="right" valign="top" background="http://s.odst.co.uk/api/hilton/images/728x90back.jpg">');
document.write('				<table class="odst" border="0" table="table" width="440" height"45" cellspacing="0" cellpadding="0" class="vstyle" style="text-align: left; margin-left: 281px!important; margin-top: 4px!important;">');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="city" style="margin-bottom:5px; text-align: left; display:inline-block;">WHERE ARE YOU GOING?</label>');
document.write('							<br />   ');
document.write('							<span class="odt_spanTextInput">');
document.write('								<input onfocus="searchAsYouType.initialize(this, false);"  id="odst_locationTextInput" type="text" value="City, airport, address, attraction, or hotel" maxlength="100" autocomplete="off" style="width:350px;"/>');
document.write('							</span>');
document.write('						</td>');
document.write('						<td width="80" valign="top" align="left">');
document.write('							<label for="guests">Guests</label>');
document.write('							<br />   ');
document.write('							<select name="guests"  style="margin-top:3px;">');
document.write('								<option value="1">1</option>');
document.write('								<option value="2">2</option>');
document.write('								<option value="3">3</option>');
document.write('								<option value="4">4 </option>');
document.write('                    	    </select>');
document.write('						</td>');
document.write('					</tr>');
document.write('				</table>');
document.write('				<table class="odst" border="0" table="table" width="440" cellspacing="0" cellpadding="0" class="vstyle" height=30 style="margin-left: 281px!important; margin-top: 4px!important;">');
document.write('					<tr valign="bottom">');
document.write('						<td valign="middle" width="178" align="left">');
document.write('							<label for="checkin" class="labelTop arrival" style="float: left; margin-top: 4px; margin-right:5px;">Arrival</label>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="from" class="odst_from" name="arrivalDate" >');
document.write('								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>');
document.write('							</span>');
document.write('						</td>');
document.write('						<td valign="middle" width="178" align="left"> ');
document.write('							<label for="checkout" class="labelTop departure" style="float: left; margin-top: 4px; margin-right:5px;">Departure</label>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="to" class="odst_to" name="departureDate" >');
document.write('								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>');
document.write('							</span> ');
document.write('						</td>');
document.write('						<td valign="top" align="left" width="84">');
document.write('							<a href="#" class="odst_findbutton" title="Find it" role="button" style="width: 30px; height: 11px;">');
document.write('');
document.write('								<span class="odst_text">Find</span>');
document.write('							</a>');
document.write('						</td>');
document.write('					</tr>');
document.write('               	 </table>');
document.write('            </td>');
document.write('        </tr>');
document.write('	</table>');
<?php
}
else if( $w == "300" && $h == "250" )
{
?>
document.write('	<table class="odst" width="300" height="250" border="0" cellpadding="8" cellspacing="0"  >');
document.write('		<tr>');
document.write('			<td align="left" valign="bottom" background="http://s.odst.co.uk/api/hilton/images/300x250back.jpg" style="padding:10px">');
document.write('				<table class="odst" border="0" table="table" width="280" height="46" cellspacing="0" cellpadding="0" class="vstyle" style="margin-top: 85px;">');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="city" style="margin-bottom:5px; display:inline-block;">WHERE ARE YOU GOING?</label>');
document.write('							<br />   ');
document.write('							<span class="odt_spanTextInput">');
document.write('								<input onfocus="searchAsYouType.initialize(this, false);"  id="odst_locationTextInput" type="text" value="City, airport, address, attraction, or hotel" maxlength="100" autocomplete="off" style="width:270px;"/>');
document.write('							</span>');
document.write('						</td>');
document.write('					</tr>');
document.write('				</table>');
document.write('				<table class="odst" border="0" table="table" width="280" height="92" cellspacing="0" cellpadding="0" class="vstyle"  >');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="checkin" class="labelTop arrival" style="margin-bottom: 7px; display: inline-block;">Arrival</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="from" class="odst_from" name="arrivalDate" >');
document.write('								<span id="arrivalPopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your arrival date must be within the next year.</span>');
document.write('							</span>');
document.write('						</td>');
document.write('						');
document.write('						<td> ');
document.write('							<label for="checkout" class="labelTop departure" style="margin-bottom: 7px; display: inline-block;">Departure</label>');
document.write('							<br/>');
document.write('							<span class="odt_spanTextInput" dir="ltr">');
document.write('								<input type="text" id="to" class="odst_to" name="departureDate" >');
document.write('								<span id="departurePopupInstruction" style="display:none;">You are now focused on a datepicker field. Press the down arrow to enter the calendar table. Once focused on the table, press left or right to navigate days. Press up or down to navigate between weeks. Enter to select. Escape to close datepicker. Your departure date must be within 4 months after your arrival date.</span>');
document.write('							</span> ');
document.write('						</td>');
document.write('					</tr>');
document.write('					<tr>');
document.write('						<td>');
document.write('							<label for="guests">Guests</label>');
document.write('							<select name="guests"  style="margin-top:5px;">');
document.write('								<option value="1">1</option>');
document.write('								<option value="2">2</option>');
document.write('								<option value="3">3</option>');
document.write('								<option value="4">4 </option>');
document.write('                    	    </select>');
document.write('						</td>');
document.write('						<td>');
document.write('							<a href="#" class="odst_findbutton" title="Find it" role="button"style="width: 45px; height: 11px;">');
document.write('								<span class="odst_text" >Find it</span>');
document.write('								<span class="arrow_icon">&nbsp;</span>');
document.write('							</a>');
document.write('						</td>');
document.write('					</tr>');
document.write('               	 </table>');
document.write('            </td>');
document.write('        </tr>');
document.write('	</table>');
<?php
}
?>
document.write('</form>');
