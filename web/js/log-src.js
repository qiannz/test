/*
源代码在这里进行压缩处理再上线 http://tool.css-js.com/
log.js src
modify:pika
date:2012-2-15
modifyDate:2014-6-6 18:26:49
*/
var _speedMark = new Date();
var _allowPiwik = false;
var _waexec = _waexec || 1;

if (_waexec == 1) {
    var _gaq = _gaq || [];

    var href = window.location.href;

    var gadomainname = 'none';
    if (/\.mplife\.com$/.test(window.location.hostname))
        gadomainname = '.mplife.com';
    else if (/\.mpdaogou\.com$/.test(window.location.hostname))
        gadomainname = '.mpdaogou.com';

    _gaq.push(['_setAccount', 'UA-8055445-10']);
    _gaq.push(['_setDomainName', gadomainname]);
    _gaq.push(['_setAllowLinker', true]);
    _gaq.push(['_setAllowHash', false]);
    //add by pika 2012-5-2
    _gaq.push(['_addOrganic', 'soso', 'w']);
    _gaq.push(['_addOrganic', 'yodao', 'q']);
    _gaq.push(['_addOrganic', 'sogou', 'query']);
    _gaq.push(['_addOrganic', 'baidu', 'word']);
    //add by pika 2012-11-23
    _gaq.push(['_addOrganic', 'so.360.cn', 'q']);

    //add by pika 2012-8-31 for editor event
    if (/\.mplife\.com$/.test(window.location.hostname) && href.indexOf(".shtml") > -1) {
        if (jQuery("meta[name=generator]").length > 0) {
            var author = jQuery("meta[name=generator]").attr("content");
            if (author != "") {
                author = escape(author).replace("%u4F5C%u8005", "").replace("%uFF1A", "").replace("%3A", "");
                _gaq.push(['_trackEvent', 'Article', 'View', unescape(author)]);
            }
        }
    }
    _gaq.push(['_trackPageview']);

    $(function () {
		var len = document.getElementsByTagName('script').length;
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[len-1]; s.parentNode.insertBefore(ga, s);
		//document.body.appendChild(ga);

        //TA 2013-7-24 BY pika ta.qq.com
        var ta = document.createElement('script'); ta.type = 'text/javascript'; ta.async = true;
        ta.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'tajs.qq.com/stats?sId=26115383';
        var s = document.getElementsByTagName('script')[len-1]; s.parentNode.insertBefore(ta, s);
		//document.body.appendChild(ta);
        /*//rem by pika 2012-10-11
        //add by pika 2012-8-31
        if (/bbs\.mplife\.com$/.test(window.location.hostname) && href.indexOf("bbs.mplife.com/showtopic-1076231") > -1) {
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            s.src = (location.protocol == 'https:' ? 'https://ssl.' : 'http://static.') + 'gridsumdissector.com/js/Clients/GWD-000558-A57672/gs.js';
            var firstScript = document.getElementsByTagName('script')[0];
            firstScript.parentNode.insertBefore(s, firstScript);
        }//test
         */
        //baidu 2014-6-6 BY PIKA
        var hm = document.createElement("script");hm.type = 'text/javascript'; hm.async = true;
        hm.src = "//hm.baidu.com/hm.js?5773358082bd20af1415163b7d02a59e";
        var s_baidu = document.getElementsByTagName("script")[len-1];
        s_baidu.parentNode.insertBefore(hm, s_baidu);
		//document.body.appendChild(hm);
    });

    _waexec = 2;
}

//Bind mousedown event for Advertisements
function bindAdEvent() {
    //add by pika 2012-3-15 for promo
    /*  if (/promo\.mplife\.com$/.test(window.location.hostname)) {
    _gaq.push(['_setAccount', 'UA-8055445-34']);
    //_gaq.push(['_trackPageview']);
    }
    else if (/bbs\.mplife\.com$/.test(window.location.hostname)) {
    //add by pika 2012-3-19 for bbs
    _gaq.push(['_setAccount', 'UA-30116839-1']);
    //_gaq.push(['_trackPageview']);
    } else {*/
    //editor
    var href = window.location.href.replace('http://', '');
    //only final page
    if (/www\.mplife\.com$/.test(window.location.hostname) && href.indexOf(".shtml") > -1) {
        //parttime 2012-1-17 BY PIKA
        var author = "";
        //new method 2012-2-6 BY PIKA
        if (jQuery("meta[name=generator]").length == 0) return;

        var author = jQuery("meta[name=generator]").attr("content");

        if (author != "") {
            author = escape(author).replace("%u4F5C%u8005", "").replace("%uFF1A", "").replace("%3A", "");

            var authorDict = ["%u6768%u6653%u9752", "%u90B5%u4E3D%u9896", "%u90B5%u9752", "%u845B%u5C27%u96EF", "%u5362%u9E23", "%u9648%u831C", "%u738B%u7EAF", "%u80E1%u4E50%u6BC5", "%u8BB8%u6587%u96EF", "%u949F%u51AC%u73B2", "%u5468%u8273%u6377", "%u987E%u7F8E%u7389", "%u5F20%u6069%u9716", "%u5B59%u5A77%u7EEE", "%u987F%u4E3D", "%u5434%u96C5%u6960", "%u5B8B%u8D3B%u5170", "%u9EC4%u51AC", "%u6797%u4E39", "%u4E01%u6BC5", "%u5218%u6C34", "%u5B8B%u4E39", "%u5F20%u6587%u6587", "%u9648%u5FD7%u534E", "%u5B59%u6A31", "%u8096%u60E0%u82BE", "%u9EC4%u4E4B%u806A", "%u7F57%u4E3D%u5A1F", "%u82CF%u777F", "%u674E%u4E4B%u6B23", "%u5218%u4F1F", "%u9EC4%u65D6", "%u6731%u71D5%u5A77", "Lorraine", "%u6881%u5FD7%u6770", "%u9EC4%u4E4B%u806A", "%u8096%u60E0%u82BE", "%u6731%u5F81", "%u5FF5%u5FF5"];

            if (authorDict.indexOf(author) > -1) {
                author = authorDict[authorDict.indexOf(author)];
                _gaq.push(['_setAccount', 'UA-28371738-1']);
                //add by pika 2012-8-31
                _gaq.push(['_trackEvent', 'Article', 'View', unescape(author)]);
                _gaq.push(['_trackPageview', '' + unescape(author)]);
            }
        }
    }
    //}

    //Optimization By LiaoWenQiang add by pika 2012-3-23
    jQuery("div[class*='Advertisement']").mousedown(function () {
        var productname = this.getAttribute('productname');
        productname = productname.length > 0 ? productname : this.getAttribute('id');
        _gaq.push(['_trackEvent', 'Advertisement', 'Click', productname]);
        _gaq.push(['_trackPageview', productname]);

        _gaq.push(['_setAccount', 'UA-20980658-1']);
        _gaq.push(['_trackEvent', 'ad', 'mousedown', this.getAttribute('id')]);
        _gaq.push(['_trackPageview', '/ad/' + this.getAttribute('productname')]);

    });
    /*
    var divs = document.getElementsByTagName("div");
    for (var i = 0; i < divs.length; i++) {
    var currentDiv = divs[i];
    if (currentDiv.className == "Advertisement") {
    currentDiv.onmousedown = function () {
    _gaq.push(['_setAccount', 'UA-20980658-1']);
    _gaq.push(['_trackEvent', 'ad', 'mousedown', this.getAttribute('id')]);
    _gaq.push(['_trackPageview', '/ad/' + this.getAttribute('productname')]);
    };
    }
    }*/

    //AddEvent for new Index 2013-7-20 BY PIKA
    if (/www\.mplife\.com$/.test(window.location.hostname)) { //index && (/^\/$/.test(window.location.pathname) || /^\/test3\/$/.test(window.location.pathname))
        jQuery("a[adname]").mousedown(function () {
            var adname = this.getAttribute('adname');
            if (adname != '') {
                if (/^\/sh\/$/.test(window.location.pathname)) {
                    _gaq.push(['_trackEvent', 'SHTrace', 'IndexClick', adname]);
                } else {
                    _gaq.push(['_trackEvent', 'Trace', 'IndexClick', adname]);
                }
                //_gaq.push(['_trackPageview', adname]);
            }
        });
    }
};

// for IE 2012-2-15 BY pika
if (!Array.prototype.indexOf) Array.prototype.indexOf = function (item, i) {
    i || (i = 0);
    var length = this.length;
    if (i < 0) i = length + i;
    for (; i < length; i++)
        if (this[i] === item) return i;
    return -1;
};
//check author
/*
function checkAuthor(author) {
    if (typeof (author) != "undefined" && author != "" && author != null) {
        if (author.indexOf(unescape("%u4F5C%u8005")) > -1) return true;
    }
    return false;
}
*/
/*
// test if jQuery is loaded
function isJQueryLoaded() {
if (typeof jQuery == 'undefined')
return false;
return true;
}

// calculate position (offset + size) of an element by jQuery selector
function calcElementPos(jqSelector) {
if (isJQueryLoaded()) {
try {
var offset = jQuery(jqSelector).offset();
var height = jQuery(jqSelector).height();
var width = jQuery(jqSelector).width();
return width + "x" + height + "+" + offset.left + "+" + offset.top;
} catch (err) {
// pass
}
}
return "";
}
*/
// this will be executed by body onload event in some pages
function CMSLog(id) {
    bindAdEvent();
}
function CMSLogEnd(id) { }
function CMSLogBegin(id) { }

//remark by pika 2012-3-7 
function piwikTrack(siteId) {
    var pkBaseURL = (("https:" == document.location.protocol) ? "https://analytics.mplife.com/piwik/" : "http://analytics.mplife.com/piwik/");
    var pwk = document.createElement('script');
    pwk.type = 'text/javascript';
    pwk.src = pkBaseURL + 'piwik.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(pwk, s);

    setTimeout(function () {
        try {
            var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", siteId);
            piwikTracker.trackPageView();
            piwikTracker.enableLinkTracking();
        } catch (err) {
            // pass
        }
    }, 1000);  // give some time for loading Piwik JS
}

//add by TianXiaoQi 2011-12-14
/*//rem by pika 2012-10-11
function MpLogStatisticsTrack(siteId) {
    //return switch  2011-12-14 by Tianxiaoqi
    //return;

    var mpBaseURL = "http://analytics.mplife.com/LogStatistics/";
    var mls = document.createElement('script');
    mls.type = 'text/javascript';
    mls.src = mpBaseURL + 'stats-min.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(mls, s);

    setTimeout(function () {
        try {
            var mpTracker = MPTracker.getTracker(mpBaseURL + "stats.aspx", siteId);

            mpTracker.trackPageView();
        } catch (err) {
            //alert(err);
            // pass
        }
    }, 1000);    // give some time for loading JS
}
*/
//remove by pika 2012-2-14
//window.onload = bindAdEvent; //support !ie? by pika

try {

    jQuery(document).ready(function () {

        bindAdEvent();

        //_allowPiwik 2012-11-23
        if (_allowPiwik) {
            var host = window.location.href.replace('http://', '');
            var hosts = host.split('/');

            if (hosts.length > 2) {
                host = host.substring(0, host.indexOf('/')) + '/' + hosts[1];
            } else {
                host = window.location.hostname;
            }

            var siteDict = {};
            siteDict["^www\.mplife\.com$|\.mpdaogou\.com$"] = 1;
            siteDict["^bbs\.mplife\.com$|^www\.mplife\.com\/bbs$"] = 2;
            siteDict["^astro\.mplife\.com$|^www\.mplife\.com\/astro$"] = 3;
            siteDict["^global\.mplife\.com$|^www\.mplife\.com\/global$"] = 4;
            siteDict["^hk\.mplife\.com$|^www\.mplife\.com\/hk$"] = 5;
            siteDict["^us\.mplife\.com$|^www\.mplife\.com\/us$"] = 6;
            siteDict["^beauty\.mplife\.com$|^www\.mplife\.com\/beauty$"] = 7;
            siteDict["^home\.mplife\.com$|^www\.mplife\.com\/home$"] = 8;
            siteDict["\.mpdaogou\.com$"] = 9;
            siteDict["^passport\.mplife\.com$"] = 10;
            siteDict["^bj\.mplife\.com$|^www\.mplife\.com\/bj$"] = 11;
            siteDict["^dress\.mplife\.com$|^www\.mplife\.com\/dress$"] = 12;
            siteDict["^shoes\.mplife\.com$|^www\.mplife\.com\/shoes$"] = 13;
            siteDict["^street\.mplife\.com$|^www\.mplife\.com\/street$"] = 14;
            siteDict["^luxury\.mplife\.com$|^www\.mplife\.com\/luxury$"] = 15;
            siteDict["^sh\.mpdaogou\.com/zhekou$|^www\.mplife\.com\/zhekou$"] = 16;
            siteDict["^digi\.mplife\.com$|^www\.mplife\.com\/digi$"] = 17;
            siteDict["^temai\.mplife\.com$|^www\.mplife\.com\/temai$|^sh\.mpdaogou\.com\/temai$|^temai\.mpdaogou\.com$"] = 18;
alert(2);
            for (site in siteDict) {
                var re = new RegExp(site);
                if (re.test(host)) {
                    //test temai 2012-11-16 BY PIKA
                    if (siteDict[site] == 18 || siteDict[site] == 2)
                        piwikTrack(siteDict[site]);
                }
            }

            //add by pika 2011-12-21
            //rem by pika 2012-10-11
            //MpLogStatisticsTrack(host);
        }//if _allowPiwik 2012-11-23
    });
} catch (e) {
    // pass
}

/*
//hack ad //PIKA
$(function () {
    if (window.top != self) { $(window.top.document).find("object").remove(); }
    //hack adsl
    function SetCookie(name, value) {
        var Days = 30;
        var exp = new Date();
        exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);
        document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
    }
    SetCookie("cn", "acuda,cuid=727036887,cpid=72061235510753700,cid=230621,cls=1339601337,ci=900,coi=900,cpt=3,caid=99990003");

});
*/