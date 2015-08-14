
var utils = {
    hideAll: function(){
        $("#notify").html("");
        $("#searchResult").html("");
    },
    updatePrompt: function(head,content){
        var html = "";
        html += "<div class='promptHead'>" + head + "</div>";
        html += "<div class='promptContent'>" + content + "</div>";
        html += "<a class='close-reveal-modal' id='myModalClose' aria-label='Close'>&#215;</a></div>";
        $("#myModal").html(html);
    },
    prompt: function(head,content){
      var html = "<div id='myModal' class='reveal-modal' data-reveal aria-labelledby='modalTitle' aria-hidden='true' role='dialog'>";
      html += "<div class='promptHead'>" + head + "</div>"
      html += "<div class='promptContent'>" + content + "</div>";
      html += "<a class='close-reveal-modal' id='myModalClose' aria-label='Close'>&#215;</a></div>";
      $("#notify").html(html);
      $('#myModal').foundation('reveal', 'open');
      $(document).foundation('reveal','reflow');

    },
    alert: function(type,data){
        var html = "<div data-alert class='alert-box "+ type +"'>";
        html += data;
        html += "<a href='#' id='alertClose' class='close'>&times;</a>";
        html += "</div>";
        $("#notify").html(html);
        $(document).foundation('alert', 'reflow');
    },

    getInfoHtml: function(data){
        var str = "<ul>"
        str += "<li> Name: "+data["name"]  + "</li>" ;
        str += "<li> Email: " + data["mail"] +"</li>";
        str += "<li> DN: " + data["dn"]  + "</li>";
        str += "</ul>";
        return str;
    },

    getNewUserInfoHtml: function(){
        var info = ""
        info += "First Name:" + $("#newUser").find("#fn").val() +"<br/>"
        info += "Middle Name:" + $("#newUser").find("#mn").val() +"<br/>"
        info += "Last Name:" + $("#newUser").find("#ln").val() +"<br/>"
        info += "Username:" +$("#newUser").find("#uname").val() +"<br/>"
        info += "Phone Number: <br>" + $("#newUser").find("#phone").val() + ":"+ $("#newUser").find("#ph").val() +"<br/>"
        info += "Groups:<ul>";
        var selected = $("#newUser").find("input[type=hidden]");
        for(var i=0;i<selected.length;i++){
            info += "<li>" + selected[i].value + "</li>";
        }
        info += "</ul>"
        return info;
    },
    getDataHtml: function(data){
        var temp = "<ul>";
        for (var key in data) {
           if (Object.prototype.hasOwnProperty.call(data, key)) {
                 var val = data[key];
                 temp += "<li>"+key + " : "+ val +"</li>";
             }  
        }
        temp += "</ul>";
        return temp;
    },
    getGroupFormHtml: function(data){
        var temp = "<form id='groupSelect'>";
        for (var i=0;i < data.length;i++){
            temp += "<div class='row'>";
            temp += "<input id='checkbox"+i+"' name='groups[]' value='"+data[i]["dn"]+"' type='checkbox'><label for='checkbox"+i+"'>"+data[i]["sAMAccountName"]+"</label>"
            temp += "</div>";
        }
        temp += "</form>"
        return temp;
    },

    getBlob : function(contents){
        var decoded = atob(contents);
        var decodedArr = new Array(decoded.length);
        for(var i=0;i< decoded.length;i++){
            decodedArr[i] = decoded.charCodeAt(i);
        }
        var decodedByteArr = new Uint8Array(decodedArr);
        var b = new Blob([decodedByteArr],{'type': 'application/zip' });
        return b;
    },
    isIE : function(){
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer, return version number
            return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
        else                 // If another browser, return 0
            return false;

        return false;
    }


}