
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
        var str = "<table>"
        for (var key in data) {
            if (data.hasOwnProperty(key)) {
                str += "<tr>";
                str += "<th> " +key+"</th>";
                if($.isArray(data[key])){
                    str += "<td><ul>"
                    for(var i=0;i<data[key].length;i++)
                        str += "<li>" + data[key][i] +"</li>"
                    str += "</ul></td>"
                } else
            str += "<td>"+data[key]  + "</td>";
                str += "</tr>";
            }
        }
        str += "</table>";
        return str;
    },
    getCookie: function(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
        }
        return "";
    },
    getNewUserInfoHtml: function(){
        var info = "<table>"
        info += "<tr><th>First Name</th>" + "<td>"+$("#newUser").find("#fn").val() +"</td></tr>"
        info += "<tr><th>Middle Name</th>" + "<td>"+ $("#newUser").find("#mn").val() +"</td></tr>"
        info += "<tr><th>Last Name</th>" + "<td>"+ $("#newUser").find("#ln").val() +"</td></tr>"
        info += "<tr><th>Username:" + "<td>"+$("#newUser").find("#uname").val() +"<td></tr>"
        info += "<tr><th>Phone(" + $("#newUser").find("#phone").val() + ")</th><td>"+ $("#newUser").find("#ph").val() +"</td></tr>"
        info += "<tr><th>Groups:</th><td><ul>";
        var selected = $("#newUser").find("input[type=hidden]");
        for(var i=0;i<selected.length;i++){
            info += "<li>" + selected[i].value + "</li>";
        }
        info += "</ul></td></tr></table>"
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
        data = data.sort(function(str1, str2){ return str1["sAMAccountName"].localeCompare(str2["sAMAccountName"])});
        var temp = "<form id='groupSelect'>";
        for (var i=0;i < data.length;i++){
            temp += "<div class='row'>";
            temp += "<input id='checkbox"+i+"' name='groups[]' value='"+data[i]["dn"]+"' type='checkbox'><label for='checkbox"+i+"'>"+data[i]["sAMAccountName"]+"</label>"
            temp += "</div>";
        }
        temp += "</form>"
        return temp;
    },
    editGroupFormHtml: function(data){
        data = data.sort(function(str1, str2){ return str1["sAMAccountName"].localeCompare(str2["sAMAccountName"])});
        var temp = "<form id='editGroups'>";
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