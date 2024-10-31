var MyAnalytics = {}

MyAnalytics.consent = function() {

	// Remplacez la valeur UA-XXXXXX-Y par l'identifiant analytics de votre site.
	var gaProperty = myanalytics_code;
	
	// Désactive le tracking si le cookie d'Opt-out existe déjà .
	var disableStr = 'ga-disable-' + gaProperty;
	var firstCall = false;

	//Cette fonction retourne la date d'expiration du cookie de consentement 
	function getCookieExpireDate() {
		var cookieTimeout = 33696000000; // 13 mois en millisecondes
		var date = new Date();
		date.setTime(date.getTime()+cookieTimeout);
		var expires = "; expires="+date.toGMTString();
		return expires;
	}

	// Fonction utile pour récupérer un cookie à partir de son nom
	function getCookie(NameOfCookie) {
		if (document.cookie.length > 0) {
			begin = document.cookie.indexOf(NameOfCookie+"=");
			if (begin != -1) {
				begin += NameOfCookie.length+1;
				end = document.cookie.indexOf(";", begin);
				if (end == -1) end = document.cookie.length;
				return unescape(document.cookie.substring(begin, end)); 
			}
		}
		return null;
	}

	//Récupère la version d'Internet Explorer, si c'est un autre navigateur la fonction renvoie -1
	function getInternetExplorerVersion() {
		var rv = -1;
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		} else if (navigator.appName == 'Netscape') {
			var ua = navigator.userAgent;
			var re = new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
				rv = parseFloat( RegExp.$1 );
		}
		return rv;
	}

	//Vérifie la valeur de navigator.DoNotTrack pour savoir si le signal est activé et est à 1
	function notToTrack() {
		if ( (navigator.doNotTrack && (navigator.doNotTrack=='yes' || navigator.doNotTrack=='1')) || ( navigator.msDoNotTrack && navigator.msDoNotTrack == '1') ) {
			var isIE = (getInternetExplorerVersion()!=-1);
			if (!isIE){ return true; }
			return false;
		}
	}

	// Fonction d'effacement des cookies
	function deleteCookie(name) {
		var path = ";path=" + "/";
		var hostname = document.location.hostname;
		if (hostname.indexOf("www.") === 0) { hostname = hostname.substring(4); }
		var domain = ";domain=" + "."+hostname;
		var expiration = "Thu, 01-Jan-1970 00:00:01 GMT";
		document.cookie = name + "=" + path + domain + ";expires=" + expiration;
	}

	// Efface tous les types de cookies utilisés par Google Analytics
	function deleteCookies() {
		var cookieNames = ["__utma","__utmb","__utmc","__utmt","__utmv","__utmz","_ga","_gat","_gid"];
		for (var i=0; i<cookieNames.length; i++) {
			deleteCookie(cookieNames[i]);
		}
	}

	return {

		showBanner: function() {
			var tag = document.getElementById('myanalytics');
			tag.innerHTML = '<div class="cookie-banner cookie-banner-choice"><span class="cookie-banner-message">'+myanalytics_message+'</span><span class="cookie-banner-accept"><a href="javascript:MyAnalytics.consent.accept();MyAnalytics.consent.hide()">Accepter</a></span><span class="cookie-banner-decline"><a href="javascript:MyAnalytics.consent.decline();MyAnalytics.consent.showDecline();">Refuser</a></span><span class="cookie-banner-infos"><a href="https://policies.google.com/technologies/partner-sites?hl=fr" target="_blank"> En savoir plus</a></span></div>';
		},

		showDNT: function() {
			var tag = document.getElementById('myanalytics');
			tag.innerHTML = '<div class="cookie-banner cookie-banner-dnt"><span class="cookie-banner-message">'+myanalytics_message_dnt+'</span><span class="cookie-banner-close"><a href="javascript:MyAnalytics.consent.hide()">&times;</a></span></div>';
		},

		showDecline: function() {
			var tag = document.getElementById('myanalytics');
			tag.innerHTML = '<div class="cookie-banner cookie-banner-deny"><span class="cookie-banner-message">'+myanalytics_message_decline+'</span><span class="cookie-banner-close"><a href="javascript:MyAnalytics.consent.hide()">&times;</a></span></div>';
		},

		decline: function() {
			document.cookie = disableStr + '=true;'+ getCookieExpireDate() +' ; path=/';
			document.cookie = 'analyticsConsent=false;'+ getCookieExpireDate() +' ; path=/';
			deleteCookies();
		},

		accept: function() {
			document.cookie = disableStr + '=false;'+ getCookieExpireDate() +' ; path=/';
			document.cookie = 'analyticsConsent=true; '+ getCookieExpireDate() +' ; path=/';

			/* Insère le tag Google Analytics */
			if (myanalytics_ga4) {
				var script = document.createElement('script');
				script.onload = function () {
					window.dataLayer = window.dataLayer || [];
					function gtag(){dataLayer.push(arguments);}
					gtag('js', new Date());
					gtag('config', myanalytics_code);
				};
				script.src = 'https://www.googletagmanager.com/gtag/js?id='+myanalytics_code;
				document.head.appendChild(script);
			} else {
				var _gaq = _gaq || [];
				_gaq.push(['_setAccount', myanalytics_code]);
				_gaq.push(['_trackPageview']);
				(function() {
					var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
					ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
					var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
				})();
				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
				ga('create', myanalytics_code, 'auto');
				ga('send', 'pageview');
			}
		},

		show: function() { document.getElementById("myanalytics").style.display = ""; },

		hide: function() { document.getElementById("myanalytics").style.display = "none"; },

		start: function() {
			var consentCookie = getCookie('analyticsConsent');
			if (!consentCookie) { // L'utilisateur n'a pas encore de cookie
				if ( notToTrack() ) { // L'utilisateur a activé DoNotTrack.
					MyAnalytics.consent.decline(); // Refuse Analytics
					MyAnalytics.consent.showDNT(); // Afffiche le message DNT
				} else {
					if (navigator.doNotTrack&&(navigator.doNotTrack=='no'||navigator.doNotTrack==0)) { // Si l'utilisateur demande à être tracké
						MyAnalytics.consent.accept(); // Accepte Analytics
						MyAnalytics.consent.hide(); // Cache la bannière
					} else { // Sinon
						MyAnalytics.consent.decline(); // Accepte Analytics par défaut
						MyAnalytics.consent.showBanner(); // Afffiche le message de choix
					}
				}
			} else { // L'utilisateur a déjà un cookie
				if (document.cookie.indexOf('analyticsConsent=false') > -1) { // Si il refuse les cookies
					MyAnalytics.consent.decline(); // Refuse Analytics
				} else {
					MyAnalytics.consent.accept(); // Accepte Analytics
				}
				MyAnalytics.consent.hide(); // Cache la bannière
			}
		}
	}

}();


/* Création de la bannière myanalytics */
var bodytag = document.getElementsByTagName('body')[0];
var div = document.createElement('div');
div.setAttribute('id','myanalytics');
bodytag.appendChild(div); 


/* Démarre la fonction CNIL */
MyAnalytics.consent.start();
