/**
 * Created by Administrator on 2016/1/7.
 */
function my_open_iframe_layer(url,style,title,layerFinish){
    //加载层
    var indexNow = layer.open({type: 2});
    var index = 0;
    if(title==null||title==''){
        $.get(url,function(data){
            layer.close(indexNow);
            index =layer.open({
                type: 1,
                shadeClose: true,
                content: data,
                style:style
            });
            layerFinish(index)
        });
    }else{
        $.get(url,function(data){
            layer.close(indexNow);
            index =layer.open({
                type: 1,
                title:[title],
                shadeClose: true,
                content: data,
                style:style
            });
            layerFinish(index);
        });
    }
    //获取页面
}