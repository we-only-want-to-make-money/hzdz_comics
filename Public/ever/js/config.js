require.config({
	baseUrl: '/assets/js/lib',
	urlArgs: "ver=1.721",
	paths: {
		'jquery': 'jquery.min',
		'zepto': 'zepto.min',
		'css': 'css.min',
		'district': 'district',
		'weixin': 'http://res.wx.qq.com/open/js/jweixin-1.0.0',
		'swiper': 'swiper/swiper.min',
		'touchslide': 'TouchSlide',
		'app': 'app',
		"layer": "layer/layer",
		'iscroll': 'iscroll',
		'raty': 'raty/jquery.raty.min',
		'lunbo': 'lubotu',
		'mobilenum':'jquery.mobilePhoneNumber',
		'mobiscroll': 'mobiscroll/mobiscroll.min',
		'echarts':'echarts.min',
		'jSignature':'jSignature.min',
		'webuploader.html5only': 'webuploader.html5only.min'
	},
    shim: {
		'zepto': {
			exports: '$'
		},
		'weixin': {
			exports: 'wx'
		},
		'swiper': {
			exports: '$',
			deps: ['zepto', 'css!../lib/swiper/swiper.min']
		},
		'touchslide': {
			deps: ['zepto']
		},
		'layer': {
			deps: ['zepto', 'css!../lib/layer/need/layer']
		},
		'app': {
			exports: "$",
			deps: ['zepto']
		},
		'slideout': {
			exports: "Slideout"
		},
		'iscroll': {
			exports: 'IScroll'
		},
		'raty': {
			deps: ['jquery']
		},
		'lunbo': {
			exports: "$",
			deps: ['jquery']
		},
		'mobilenum':{
			deps: ['zepto']
		},
		'mobiscroll': {
			deps: ['zepto', 'css!../lib/mobiscroll/mobiscroll.min.css']
		},
		'echarts': {
			exports: "$"
		},
		'jSignature':{
			exports:"$",
			deps:['jquery']
		},
		'webuploader.html5only':{
			exports: "WebUploader",
			deps:['jquery']
		}
    }
});