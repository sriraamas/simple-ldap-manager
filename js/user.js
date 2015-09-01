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
                    if(utils.isIE()){
                        var b = utils.getBlob(data["data"]["contents"]);
                        window.navigator.msSaveBlob(b,data['data']['filename'])
                        storage.contents['blob'] = b;
                        storage.contents['filename'] = data['data']['filename'];
                        utils.alert("success", "Your VPN Credentials should begin downloading now! If not, Click <a href='#' onclick='handlers.user.ieDownload()'> here </a>");
                    } else {
                        utils.alert("success", "VPN Credentials generated Successfully. They will be downloaded shortly. If it failed to download, click <a id='downId' download = '"+data["data"]["filename"] +"' href='data:application/zip;base64," + data['data']['contents'] + "'>here</a>");
                        var dlink = document.getElementById("downId");
                        dlink.click();
                    }
                } else {
                    utils.alert("warning", "VPN Credentials failed to generate. Reason: "+ data["errors"][0]);
                }

            },
            genSsh: function(data){
                if(data["success"]){
                    form.clearInputs("genSsh");
                    if(utils.isIE()){
                        var b = utils.getBlob(data["data"]["contents"]);
                        window.navigator.msSaveBlob(b,data['data']['filename'])
                        storage.contents['blob'] = b;
                        storage.contents['filename'] = data['data']['filename'];
                        utils.alert("success", "Your SSH Credentials should begin downloading now! If not, Click <a href='#' onclick='handlers.user.ieDownload()'> here </a>");
                    } else {
                        utils.alert("success", "SSH Credentials generated Successfully. They will be downloaded shortly. If it failed to download, click <a id='downId' download = '"+data["data"]["filename"] +"' href='data:application/zip;base64," + data['data']['contents'] + "'>here</a>!");
                        var dlink = document.getElementById("downId");
                        dlink.click();
                    }
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