var nSlice_count = 200,//分段数
    nFactCount,           //实际分段数
    nMin_size      = 0.5,//最小分段大小(M)
    nMax_size     = 5,  //最大分段大小(M)
    nFactSize,           //实际分段大小
    nCountNum     = 0,  //分段标号
    sFile_type,           //文件类型
    nFile_load_size,   //文件上传部分大小
    nFile_size,           //文件大小
    nPreuploaded = 0,  //上一次记录上传部分的大小
    bIs_uploading= false,//是否上传中
    bStart_upload= false,//是否开始上传
    bEnd_upload  = false,//是否上传完成
	
	
	timestamp=new Date().getTime();//时间戳
	
function init(){
    var $con = document.getElementById("submit").value;

    bStart_upload = ($con=="上传"?true:false);
    if(bStart_upload)
    {
        if(!bEnd_upload)
        document.getElementById("submit").value = "暂停";
    }
    else
    {
        clearTimeout('timer');
        document.getElementById("submit").value = "上传";
    }
    if(!bEnd_upload && bStart_upload)
    startUpload();    
}

function startUpload(){
    var form = document.forms["upload_form"];
	
	var rand_tmp = timestamp+RndNum(3);//随机文件名前缀
	
    if(form["file"].files.length<=0)
    {
        alert("请先选择文件，然后再点击上传");
        return;
    }    

    var file = form["file"].files[0];

    var get_file_message = (function(){

        var get_message = {
            get_name:function(){
                return file.name;
            },
            get_type:function(){
                return file.type;

            },
            get_size:function(){
                return file.size;
            },
            getAll:function(){
                return {
                    fileName : this.get_name(),
                    fileSize : this.get_size(),
                    fileType : this.get_type()    
                }
            }
        };
        return get_message;
    })();

    var conversion = (function(){
        var unitConversion = {
            bytesTosize:function(data){
                var unit = ["Bytes","KB","MB","GB"];
                var i = parseInt(Math.log(data)/Math.log(1024));
                return (data/Math.pow(1024,i)).toFixed(1) + " " + unit[i];
            },
            secondsTotime:function(sec){
                var h = Math.floor(sec/3600),
                    m = Math.floor((sec-h*3600)/60),
                    s = Math.floor(sec-h*3600-m*60);
                if(h<10) h = "0" + h;
                if(m<10) m = "0" + m;
                if(s<10) s = "0" + s;

                return h + ":" + m + ":" + s + ":";
            }
        };

        return unitConversion;
    })();

    //start sending 
    var reader = new FileReader();
    var timer;

    var fProgress = function(e){
        var fSize = get_file_message.getAll().fileSize;
        timer = setTimeout(uploadCount(e,fSize,conversion),300);
    };

    var floadend = function(e){
        if(reader.error){alert("上传失败,出现未知错误");clearTimeout(timer);return;}
        clearTimeout(timer);
        if(nCountNum+1!=nFactCount)
        {
            if(bStart_upload)
            {
                nCountNum++;
                uploadStart();
                return;
            } else {
                document.querySelector(".speed").innerHTML = "0k/s";
                document.querySelector(".left_time").innerHTML = "剩余时间 | 00:00:00";
                return;
            }        
        }

        bEnd_upload = true;
        document.querySelector(".speed").innerHTML = "0k/s";
        document.querySelector(".left_time").innerHTML = "剩余时间 | 00:00:00";
        document.querySelector(".upload_percent").innerHTML = "100.00%";
        document.getElementById("submit").value = "上传";
        document.querySelector(".upload_bar").style.width = "100%";

        var $res = JSON.parse(e.target.responseText);
		console.log($res);
        filePreview($res);
        if($res.res=="success") {
            bIs_uploading =true;
            document.querySelector("#package_url").value=$res.package_url;
            window.location.href = "./efucms.php?m=Admin&c=Video&a=index";
        }
        document.querySelector(".isCompleted").innerHTML="上传状态: " + (bIs_uploading ?"上传完成":"正在上传..");
    };

    var uploadStart = function(){
        var get_all = get_file_message.getAll();
        var start = nCountNum * nFactSize,
            end   = Math.min(start+nFactSize,get_all.fileSize);

        var fData = new FormData();

        fData.append("file",file.slice(start,end));
        fData.append("name",file.name);
        fData.append("size",file.size);
        fData.append("type",file.type);
		fData.append("title",$('#title').val());
		fData.append("summary",$('#summary').val());
		//fData.append("title",$('#title').val());
		fData.append("cover_pic",$('#cover_pic').val());
		fData.append("author",$('#author').val());
		fData.append("price",$('#price').val());
		fData.append("trytime",$('#trytime').val());
		//fData.append("pic2",$('#pic2').val());
		//fData.append("urls",$('#urls').val());
        fData.append("totalCount",nFactCount);
        fData.append("indexCount",nCountNum);
		console.log($('#title').val()+$('#summary').val()+$('#cover_pic').val()+$('#author').val()+$('#trytime').val());
		//增加临时文件名
		fData.append("rand_tmp",rand_tmp);
        
        //fData.append("crc32",CRC32(file.size));
        
        fData.append("trueName",file.name.substring(0,file.name.lastIndexOf(".")));

        if(!sFile_type)
        sFile_type = file.type.substring(0,file.type.indexOf("/"));
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress",fProgress,false);
        xhr.addEventListener("load",floadend,false);
        xhr.addEventListener("error",errorUp,false);
        xhr.addEventListener("abort",abortUp,false);
		xhr.open("POST","./efucms.php?m=Admin&c=Video&a=upload");  //后台php路径
        xhr.send(fData);
    };

    reader.onloadstart = function(){
        var get_all = get_file_message.getAll(),
            fName = get_all.fileName,
            fType = get_all.fileType,
            fSize = conversion.bytesTosize(get_all.fileSize);

        document.querySelector(".upload_message_show").style.display = "block";
        document.querySelector(".upload_file_name").innerHTML ="文件名称: " + fName;
        document.querySelector(".upload_file_type").innerHTML ="文件类型: " + fType;
        document.querySelector(".upload_file_size").innerHTML ="文件大小: " + fSize;
        document.querySelector(".isCompleted").innerHTML       ="上传状态: " + (bIs_uploading?"完成":"正在上传中..");

        nFactSize = Math.floor(get_all.fileSize/nSlice_count);
        nFactSize = (nFactSize>=nMin_size*1024*1024?nFactSize:nMin_size*1024*1024);
        nFactSize = (nFactSize<=nMax_size*1024*1024?nFactSize:nMax_size*1024*1024);
        nFactCount= Math.ceil(get_all.fileSize/nFactSize);

        uploadStart();
    };


    reader.readAsBinaryString(file);
}

function uploadCount(e,fSize,conversion){
    var upSize = e.loaded+nCountNum*nFactSize,
        perc = (upSize*100/fSize).toFixed(2) + "%";
    var speed = Math.abs(upSize - nPreuploaded);
    if(speed==0){clearTimeout("timer");return;}
    var leftTime = conversion.secondsTotime(Math.round((fSize-upSize)/speed));
    speed = conversion.bytesTosize(speed)+"/s";
    document.querySelector(".speed").innerHTML = speed;
    document.querySelector(".left_time").innerHTML = "剩余时间 | " + leftTime;
    document.querySelector(".upload_percent").innerHTML = perc;
    document.querySelector(".upload_bar").style.width = perc;
    nPreuploaded = upSize;
}

function messageChange(){
    document.querySelector(".upload_file_name").innerHTML ="文件名称: " ;
    document.querySelector(".upload_file_type").innerHTML ="文件类型: " ;
    document.querySelector(".upload_file_size").innerHTML ="文件大小: " ;
    document.querySelector(".isCompleted").innerHTML       ="上传状态: " ;
    document.querySelector(".upload_bar").style.width = "0%";
    document.querySelector(".upload_percent").innerHTML = "0%";
    //document.querySelector(".upload_file_preview").innerHTML ="";
    document.querySelector(".upload_message_show").style.display = "none";
}

function clearUploadFile(){
    var e = e || event;
    e.stopPropagation();
    e.preventDefault();
    document.getElementById("file").value = "";
    bStart_upload = false;
    messageChange();
}


function fileReady(){
    bIs_uploading = false;
    bEnd_upload = false;
    nCountNum = 0;
    bStart_upload = false;
    messageChange();
}


function errorUp(){
    bStart_upload = false;
    document.querySelector(".upload_file_error").innerHTML = "上传过程中出错";
}

function abortUp(){
    bStart_upload = false;
    document.querySelector(".upload_file_error").innerHTML = "网络故障，请检查重试";
}

function filePreview($src){
    var ftype = sFile_type;
    var $temp;
    var IMGMaxHeight = document.querySelector(".upload_message_show").offsetHeight;
    switch(ftype){
        case "image" :
        $temp = '<img src="source/'+$src.url+'" style="max-height:'+IMGMaxHeight+'px;margin-left:30%;">';
        break;
        case "audio" :
        $temp = '<audio src="source/'+$src.url+'" controls="controls"></audio>';
        break;
        case "video" :
        $temp = '<video src="source/'+$src.url+'" controls="controls"></video>';
        break;
        case "rar":
        $temp = '<span>rar文件；</span>';
        case "zip":
        $temp = '<span>zip文件</span>';
    }
    /*

    if(IsPreview)
    document.querySelector(".upload_file_preview").innerHTML = $temp;*/
}

//随机数
function RndNum(n){
  var rnd="";
  for(var i=0;i<n;i++)
     rnd+=Math.floor(Math.random()*10);
  return rnd;
}