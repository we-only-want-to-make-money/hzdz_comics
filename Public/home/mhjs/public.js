/**
 * wzb 2017-1-6
 */
var cid = 0;
var sort = 0;
var bid = 0;
var p = 1;

function formatTjNumber(cnt){
    cnt = parseInt(cnt);
    if(cnt>=10000){
        return Math.floor(cnt/1e4 * 10)/10+'万';
    }else{
        return ""+cnt;
    }
}
Date.prototype.format = function(fmt) { 
    var o = { 
       "M+" : this.getMonth()+1,                 //月份
       "d+" : this.getDate(),                    //日
       "h+" : this.getHours(),                   //小时
       "m+" : this.getMinutes(),                 //分
       "s+" : this.getSeconds(),                 //秒
       "q+" : Math.floor((this.getMonth()+3)/3), //季度
       "S"  : this.getMilliseconds()             //毫秒
   }; 
   if(/(y+)/.test(fmt)) {
           fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
   }
    for(var k in o) {
       if(new RegExp("("+ k +")").test(fmt)){
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        }
    }
   return fmt; 
}
//update byefucms ios微信下不能直接用new Date(time) 这种格式
function formatDateTime(fmt,time){
    return (new Date(Date.parse(time.replace(/-/g, "/")))).format(fmt);
}
function showThirdImg(img){
    return img?img.replace('http:',''):img;
}
function showBookTags(tags){
    if(!tags) return '';
    tagsArr = tags.split(',');
    if(tagsArr.length>=2){
        return '<label class="tag">'+tagsArr[0]+'</label><label class="tag" style="margin-left:4px;">'+tagsArr[1]+'</label>';
    }else{
        return '<label class="tag">'+tagsArr[0]+'</label>';
    }
}

//Ajax 请求 json post
function AjaxJson(url,data,successFunc,errorFunc){
    var data = data || "{}";
    successFunc = successFunc || null;
    errorFunc = errorFunc || null;
    if(!url || url==='#')
        return false;

    $.ajax({
        type: 'POST',
        url: url,
        data:data,
        dataType: 'json',
        success: function(data) {
            try{ successFunc(data); }catch(e){window.console&&window.console&&console.log("页面动态加载js异常",e);}
        },
        error: function(xhr, type) {
            try{ errorFunc&&errorFunc(data); }catch(e){window.console&&window.console&&console.log("页面动态加载不成功",e);}
        },
    })
    //阻止冒泡
    return false;
}

// 页面提示信息
function bh_msg_tips(msg){
    var oMask = document.createElement("div");
    oMask.id = "bh_msg_lay";
    oMask.style.position="fixed";
    oMask.style.left="0";
    oMask.style.top="50%";
    oMask.style.zIndex="100";
    oMask.style.textAlign="center";
    oMask.style.width="100%";
    oMask.innerHTML =  "<span style='background: rgba(0, 0, 0, 0.65);color: #fff;padding: 10px 15px;border-radius: 3px; font-size: 14px;'>" + msg + "</span>";
    document.body.appendChild(oMask);
    setTimeout(function(){$("#bh_msg_lay").remove();},2000);
}

/**********************************
 * return top
 **********************************/
// 页面加载执行
window.onload = function() {
    return_top();
}

function return_top(){
    var obtn = document.getElementById('icon-top');
    try{
        //获取页面可视区的高度
        // <!doctype html> 必须要有值clientHeight才有用
        var clientHeight = document.documentElement.clientHeight;
        var timer = null;
        var isTop = true;
        var osTop = document.documentElement.scrollTop || document.body.scrollTop;
        if (osTop >= clientHeight) {
            obtn.style.display = "block";
        } else {
            obtn.style.display = "none";
        };
        // 滚动条滚动时触发
        window.onscroll = function() {
            var osTop = document.documentElement.scrollTop || document.body.scrollTop;
            if (osTop >= (clientHeight/2)) {
                obtn.style.display = "block";
            } else {
                obtn.style.display = "none";
            };
            if (!isTop) {
                clearInterval(timer);
            };
            isTop = false;
        }
        obtn.onclick = function() {
            document.documentElement.scrollTop = document.body.scrollTop = 0;
        }
    }catch(e){ return false; }
}
// 首页 猜你想
function get_other_books(bid){
    var url = "/Index/ajax_rand_book";
    var data = {bid:bid,limit:6}
    AjaxJson(url,data,function(data){
        if(data.status*1 == 1){
            $("#other_book").html(data.data);
        }else{
            bh_msg_tips(data.info);
        }
    })
}

// 搜索页搜索
function key_search_list(){
    var key_v = $('#search_keyword').val();
    if(key_v == ''){
        return false;
    }
    var url = "/book/ajax_search";
    var data = {key:key_v,p:1}
    $("#bhloading").show();
    AjaxJson(url,data,function(data){
        if(data.status*1 == 1){
            $('#page_html').html(data.data);
            $('#show_cleare_btn').show();
            $('#show_sear_btn').hide();
        }else{
            bh_msg_tips(data.info);
        }
        $("#bhloading").hide();
    })
}

// 实时搜索
function keyup_search(obj,type){
    $('#show_cleare_btn').hide();
    $('#show_sear_btn').show();
    $('#closeid').show();
    return false;
    var key_v = $(obj).val();
    if( parseInt(key_v.length) >0  ){
        $("#page_html").empty();
        var url = "/book/auto_search";
        var data = {key:key_v,type:type}
        AjaxJson(url,data,function(data){
            if(data.status*1 == 1){
                if(data.data != ''){
                    $("#page_html").html(data.data);
                    $('#show_cleare_btn').hide();
                    $('#show_sear_btn').show();
                }
            }
        })
    }
}

// 清除输入框的文字
function close_clear(){
    $('#closeid').hide();
    $('#search_keyword').val('');
    $('#show_cleare_btn').show();
    $('#show_sear_btn').hide();
}

//首页显示搜索框
function show_ser_box(){
    $("#show_ser_box").show();
}
//首页隐藏搜索框
function hide_ser_box(){
    $("#show_ser_box").hide();
    $("#search_keyword").val("");
    close_clear();
}
//首页点击搜索跳转到搜索页面
// 搜索页搜索
function key_search_href() {
    var key_v = $('#search_keyword').val();
    if (key_v == '') {
        return false;
    }
    hide_ser_box();
    window.location.href = "index.php?m=Home&c=Mh&a=search&key="+key_v;
}

// 列表分页获取
var autoready=1;
function list_page(url,data,no,id){
    var scroll_obj = $("div[data-scroll='true']").size()>0?$("div[data-scroll='true']"):window;
    $(scroll_obj).bind("scroll", function (event) {
        //滚动条到网页头部的 高度，兼容ie,ff,chrome
        var top = document.documentElement.scrollTop + document.body.scrollTop;
        var textheight = $(document).height();  //网页的高度
        // 网页高度-top-当前窗口高度
        if (textheight - top - $(window).height() <= 60){
            if(autoready==1) {
                autoready=0;
                get_page_data(url,data,no,id);
            }
        }
    });
}
//请求分页数据
function get_page_data(url,data,no,id){
    autoready=0;
    var box_id = id || '#html_box';
    p = data['p'];
    $("#bhloading").show();
    AjaxJson(url,data,function(res){
        if(res.status != 0&&res.data.length>0){
            p++;
            data['p'] = p;
            $(box_id).append(laytpl($("#itemTpl").text()).render(res.data));
            trigger_lazy_ajax();
            autoready=1;
        }
        $("#bhloading").show();
    })
    
}

// 书籍详情页 显示和隐藏图书相关信息
function toggle_book_info(obj){
    if($('#book_rele_info').is(':hidden')){
        $(obj).html("收起<i class='icon-retract'></i>");
    }else{
        $(obj).html("展开<i class='icon-open'></i>");
    }
    $('#book_rele_info').toggle();
    window.scrollTo(0,document.body.scrollHeight);
}

// 书籍详情页 显示书籍简介
function bh_book_title_show(){
    $(".bh_book_title_show").hide();
    $(".bh_book_title_hide").show();
}
// 书籍详情页 隐藏书籍简介
function bh_book_title_hide(){
    $(".bh_book_title_show").show();
    $(".bh_book_title_hide").hide();
}
//手动触发用的延迟加载
function trigger_lazy_ajax(){
    if(window.$&&$.fn&&$.fn.lazyload){
        $(".img-ajax-lazy").lazyload({
            threshold:100,//距离200px自动加载下一张图片
            effect:'fadeIn',
            load:function(x,y){$(this).removeClass('img-ajax-lazy');}
        });
    }else{
        $(".img-ajax-lazy").each(function () {
            var that = $(this);
            var src = that.attr("data-original");
            that.attr("src",src);
            that.removeClass("img-ajax-lazy");
            that.removeAttr("data-original");
        });
    }
}
// 书籍详情页 其他在作品换一换
function get_other_books_info(bid){
    var url = "/book/ajax_rand_list_book";
    var data = {bid:bid}
    AjaxJson(url,data,function(data){
        if(data.status*1 == 1){
            $("#other_book").html(laytpl($("#itemTpl").text()).render(data.data));
            //update byefucms 20170926 对需要计算占位图的图片位置设置宽高，其他如果有需要，加上cal-placeholder这个类即可
            $("img.cal-placeholder").each(function(i,x){x.width>0&&x.height&&$(x).css({width:x.width,height:x.height});});
            trigger_lazy_ajax();
        }else{
            bh_msg_tips(data.info);
        }
    })
}

// 加入和移除书架
var is_on_book = 0;
function user_add_book_shelf(obj){
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    if(is_on_book == 1 || bid<1){
        return false;
    }
    is_on_book = 1;
    var book_shelfurl = "/book/ajax_add_books";
    var book_shelfdata = {bid:bid,cid:cid,sort:sort};
    AjaxJson(book_shelfurl,book_shelfdata,function(res){
        is_on_book = 0;
        if(res.status*1 == 1){
            $(obj).toggleClass("gray");
            var is_gray_ = $(obj).hasClass("gray");
            if(is_gray_){
                $(obj).html("已收藏");
            }else{
                $(obj).html('<svg width="10" height="10" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill="#fff" d="M20 8h-8V0H8v8H0v4h8v8h4v-8h8V8"/></svg>收藏');
            }
        }else{
            bh_msg_tips(res.info);
        }
    })
};


function toggle_wx_lay(){
    $(".bh_wxgz_lay").toggle();
}

// 加入和移除书架
function oneadd_userbook_shelf(obj){
    if(parseInt(uid)*1 < 1){
       show_login();
       return;
    }
    if(is_on_book == 1 || bid<1){
        return false;
    }

  /*  if($(obj).hasClass('active')){
        bh_msg_tips("已经收藏啦！");
        return;
    }*/

    if($(obj).hasClass('active')){
        $(obj).removeClass('active');
        $('.shelf').removeClass('active');
    }else{
        $(obj).addClass('active');
        $('.shelf').addClass('active');
    }

    is_on_book = 1;
    var userbookurl = "/book/ajax_add_books";
    var userbookdata = {bid:bid,cid:cid,sort:sort};
    AjaxJson(userbookurl,userbookdata,function(res){
        if(res.status*1 != 1){
            bh_msg_tips(res.info);
        }
        is_on_book = 0
    });
}

function follow_userbook_shelf(){
    if(parseInt(uid)*1 < 1){
        return false;
    }
    if(is_on_book == 1 || bid<1){
        return false;
    }
    if(!$('.shelf').hasClass('active')){
        $('.shelf').addClass('active');
        is_on_book = 1;
        var userbookurl = "/book/add_user_book_shelf";
        var userbookdata = {bid:bid,cid:cid,sort:sort}
        AjaxJson(userbookurl,userbookdata,function(res){
            is_on_book = 0
        })
    }
};

// 选择星星
function choice_star_box(index){
    $("#star_box li a").removeClass("active");
    for (var i= 0;i<index;i++){
        $("#star_box li a").eq(i).addClass("active");
    }
    star = index;
}
// 发表评论
var star = 0;
var pid = 0;
var is_request= false;
function publist_comment(){
    if(is_request){
        return ;
    }
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    if(parseInt(bid)*1 < 1){
        bh_msg_tips("无法发表评论");
        return;
    }
    var comment_mess = $("#comment_mess").val();
    if(!comment_mess){
        bh_msg_tips("请填写留言内容");
        return;
    }
    var publist_url = "/Comment/add_comment";
    var publist_data = {bid:bid,mess:comment_mess,star:star,pid:pid,btype:btype};
    is_request = true;
    AjaxJson(publist_url,publist_data,function(res){
        is_request = false;
        if(res.status*1 == 1){
            bh_msg_tips('评论成功');
            if(btype==1){
                setTimeout(function(){window.location.href = '/comment/list_comment/?novel_id='+bid;},500);
            }else{
                setTimeout(function(){window.location.href = '/comment/list_comment/?book_id='+bid;},500);
            }
        }else {
            bh_msg_tips(res.info);
        }

    })
}

$(function(){
    $("#comment_mess").keydown(function(e){
        var curKey = e.which; 
        if(curKey == 13){ 
            publist_comment(); 
            return false; 
        }  
    });
    $("#reply_mess").keydown(function(e){
        var curKey = e.which; 
        if(curKey == 13){ 
            $("#reply_action").click(); 
            return false; 
        }  
    });
})

// 回复评论
function replay_comment(pid,bid){
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    if(parseInt(bid)*1 < 1 || parseInt(pid)*1 < 1){
        bh_msg_tips("无法发表评论");
        return;
    }
    var replay_mess = $("#reply_mess").val();
    if(!replay_mess){
        bh_msg_tips("请填写回复内容");
        return;
    }
    var publist_url = "/Comment/add_comment";
    var publist_data = {bid:bid,mess:replay_mess,star:5,pid:pid};
    AjaxJson(publist_url,publist_data,function(res){
        if(res.status*1 == 1){
            bh_msg_tips("回复成功");
            $("#reply_mess").val('');
            $("#replie_count").text(parseInt($("#replie_count").text())+1);
            //append这条数据在最后，那就意味着要返回这条回复数据
            $("#html_box").append(laytpl($("#itemTpl").text()).render(res.data))
        }else {
            bh_msg_tips(res.info);
        }
    })
}

// 评论点赞
function comment_dianzan(obj,id){
    if(parseInt(id)*1<1){
        bh_msg_tips("点赞失败");
        return;
    }
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    if($(obj).hasClass('active')){
        bh_msg_tips("您已经点过赞了");
        return;
    }
    var old_zan = $(obj).find("span").html();
    var dianzan_url = "/Comment/ajax_comment_dianzan";
    var dianzan_data = {reply_id:id,book_id:bid,btype:btype};
    AjaxJson(dianzan_url,dianzan_data,function(res){
        if(res.status*1 == 1){
            bh_msg_tips("点赞成功！");
            old_zan++;
            $(obj).addClass('active').find("span").html(old_zan);
        }else{
            bh_msg_tips(res.info);
        }
    })
}

function chapter_dianzan(obj,id){
    if(parseInt(id)*1<1){
        bh_msg_tips("点赞失败");
        return;
    }
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
  /*  if($(obj).hasClass('active')){
        bh_msg_tips("已经赞过啦！");
        return;
    }*/
    var old_zan = $(".zan").find("span").html();

    if($(obj).hasClass('active')){
        $(".zan").removeClass('active').find("span").html(--old_zan);
        $(".menu-zan").removeClass('active');
    }else{
        $(".zan").addClass('active').find("span").html(++old_zan);
        $(".menu-zan").addClass('active');
    }

    var dianzan_url = "/book/ajax_chapter_dianzan";
    var dianzan_data = {chapter_id:id,book_id:bid};
    AjaxJson(dianzan_url,dianzan_data,function(res){
        if(res.status*1 != 1){
            bh_msg_tips(res.info);
        }
    })
}

function book_pressing(obj,id){
    if(parseInt(id)*1<1){
        bh_msg_tips("催更失败");
        return;
    }
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    if($(obj).hasClass('btn-gray')){
        bh_msg_tips("已经催更过啦！");
        return;
    }
    var dianzan_url = "/book/ajax_book_pressing";
    var dianzan_data = {book_id:bid};
    AjaxJson(dianzan_url,dianzan_data,function(res){
        var old_zan = $(obj).find("span").html();
        if(res.status*1 == 1){
            $(obj).addClass('btn-gray').find("span").html(++old_zan);
            bh_msg_tips('催更成功！');
        }else{
            bh_msg_tips(res.info);
        }
    })
}

function public_setCookie(name,value){ 
    var exp = new Date(); 
    exp.setTime(exp.getTime() + 2592000000); //30day
    document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path=/";  
}

// 首页切换男频女频
function bh_qiesexv(obj,sexv){
    public_setCookie('qrmh_book_sex',sexv);
    window.location.reload();
}

//Ajax 请求 json post
function AjaxJsonP(url,data,successFunc,errorFunc){
    var data = data || "{}";
    successFunc = successFunc || null;
    errorFunc = errorFunc || null;
    if(!url || url==='#')
        return false;

    $.ajax({
        type: 'POST',
        url: url,
        data:data,
        dataType: 'jsonp',
        success: function(data) {
            try{ successFunc(data); }catch(e){}
        },
        error: function(xhr, type) {
            try{ errorFunc(data); }catch(e){}
            window.console&&console.log("页面动态加载不成功，请与管理员联系");
        },
    })
    //阻止冒泡
    return false;
}

function comment_report_show(obj,id){
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    var $modal_report = $("#report-container");
    var $objpost = $(obj).parents('.body');
    $("#comment_report_username",$modal_report).text($('.t',$objpost).text());
    $("#comment_report_postcontent",$modal_report).text($('.txt a',$objpost).text());
    $("#comment_report_postid",$modal_report).val(id);
    $(".comment-report").show();
}
function hide_comment_report(){
    $(".comment-report").hide();
}
function do_comment_report(){
    var $modal_report = $("#report-container");
    var postid = $("#comment_report_postid",$modal_report).val();
    if(parseInt(postid)*1<1){
        bh_msg_tips("举报失败");
        return;
    }
    if(parseInt(uid)*1 < 1){
        show_login(); //  未登陆
        return;
    }
    var report_type = $("#report_type input[name='report']:checked",$modal_report).val();
    if(!report_type){
        bh_msg_tips("请填写举报类型");
        return;
    }
    var report_cause = $("#report_cause",$modal_report).val();
    if(!report_cause){
        bh_msg_tips("请填写举报理由");
        return;
    }
    var report_url = "/comment/ajax_comment_report";
    var report_data = {post_id:postid,book_id:bid,report_cause:report_cause,report_type:report_type,btype:btype};
    AjaxJson(report_url,report_data,function(res){
        if(res.status*1 == 1){
            comment_report_success();
        }else {
            bh_msg_tips(res.info);
        }
    })
}
function comment_report_success(){
    hide_comment_report()
    $(".comment_report_success").show();
}
function hide_comment_report_success(){
    $(".comment_report_success").hide();
}

function chapList_data(url,data){
    AjaxJson(url,data,function(res){
        if(res.status*1 == 1){
            autoready = 1;
            $('#html_box').html(laytpl($("#chapterTpl").text()).render(res.data));
            if(res.data.length > 16){
                $('.chapter_all').show();
            }else{
                $('.chapter_all').hide();
            }
        }else{
            window.console&&console.log(res.info)
        }
    })
}