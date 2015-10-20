$(window).load(function() {
    $("#passForm").submit( function(event){
        if(form.canSubmit(this)){
            handlers.user.resetPwd(event);
        }
        event.preventDefault();
    });

    $("#genVpn").submit( function(event){
        if(form.canSubmit(this)){
            handlers.user.genVpn(event);
        }
        event.preventDefault();
    });
    $("#genSsh").submit( function(event){
        if(form.canSubmit(this)){
            handlers.user.genSsh(event);
        }
        event.preventDefault();
    });
    $("#user-ss-topright").addClass("active");
    $("#admin-ss-topright").removeClass("active");

});

// From http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
var b64toBlob = function(b64Data, contentType, sliceSize) {
    contentType = contentType || '';
    sliceSize = sliceSize || 512;

    var byteCharacters = atob(b64Data);
    var byteArrays = [];

    for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
        var slice = byteCharacters.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    var blob = new Blob(byteArrays, {type: contentType});
    return blob;
};

var handlers = {
    user: {
        resetPwd: function(event){
            var alertClose = document.getElementById("alertClose");
                if(alertClose)
                    alertClose.click();
            var token = utils.getCookie("xsrftoken");

            var formDom = event.target;
            form.submit.loading.show(formDom);
            form.submit.disable(formDom,"Changing Password");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() +"&xsrftoken="+token, handlers.user.action.resetPwd);
            req.fail(function(){
                utils.alert("alert","Request to Reset Password Failed");
            });
            req.always(function(){
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });
        },
        genVpn: function(event){
            var alertClose = document.getElementById("alertClose");
                if(alertClose)
                    alertClose.click();
            var token = utils.getCookie("xsrftoken");
            var formDom = event.target;
            form.submit.loading.show(formDom);
            form.submit.disable(formDom,"Regenerating VPN Credentials");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() +"&xsrftoken="+token, handlers.user.action.genVpn);
            req.fail(function(){
                utils.alert("alert","Request to Generate VPN Credentials Failed");
            });
            req.always(function(){
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });

        },
        genSsh: function(event){
            var alertClose = document.getElementById("alertClose");
                if(alertClose)
                    alertClose.click();
            var token = utils.getCookie("xsrftoken");
            var formDom = event.target;
            form.submit.loading.show(formDom);
            form.submit.disable(formDom,"Regenerating SSH Credentials");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() +"&xsrftoken="+token, handlers.user.action.genSsh);
            req.fail(function(){
                utils.alert("alert","Request to Generate SSH Credentials Failed");
            });
            req.always(function(){
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });

        },
        action : {
            resetPwd: function(data){
                if(data["success"]){
                    form.clearInputs("passForm");
                    utils.alert("success", "Password Successfully Changed. Changes will take effect in 15 seconds.");
                }  else {
                    utils.alert("warning", "Password Change failed. Reason : "+data["errors"][0]);
                }
            },
            genVpn: function(data){
                if(data["success"]){
                    form.clearInputs("genVpn");
                    var dataBlob = b64toBlob(data["data"]["contents"], 'application/zip');
                    saveAs(dataBlob, data["data"]["filename"])
                    utils.alert("success", "VPN Credentials generated Successfully. They will be downloaded shortly. If it failed to download, click <a id='downId' download = '"+data["data"]["filename"] +"' href='data:application/zip;base64," + data['data']['contents'] + "'>here</a>");
                } else {
                    utils.alert("warning", "VPN Credentials failed to generate. Reason: "+ data["errors"][0]);
                }

            },
            genSsh: function(data){
                if(data["success"]){
                    form.clearInputs("genSsh");
                    var dataBlob = b64toBlob(data["data"]["contents"], 'application/zip');
                    saveAs(dataBlob, data["data"]["filename"])
                    utils.alert("success", "SSH Credentials generated Successfully. They will be downloaded shortly. If it failed to download, click <a id='downId' download = '"+data["data"]["filename"] +"' href='data:application/zip;base64," + data['data']['contents'] + "'>here</a>!");
                } else {
                    utils.alert("warning", "SSH Credentials failed to generate. Reason: "+ data["errors"][0]);
                }
            }
        },
        ieDownload : function(){
            if(storage.contents.hasOwnProperty("blob")){
                var blob = storage.contents['blob'];
                if(storage.contents.hasOwnProperty("filename")){
                    var filename = storage.contents['filename']
                    window.navigator.msSaveBlob(blob,filename);
                }
            }
        }
    }

}

var storage = {
    contents: {

    }
}