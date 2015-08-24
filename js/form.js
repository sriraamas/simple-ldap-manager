
var form = {
    canSubmit : function(formDom) {
        var flag = true
        $(formDom).find("input[required]").each(function(){
            if($(this)[0].value == ""){
                flag = false;
            }
        });
        return flag;
    },
    submit: {
        disable: function(formDom,text){
            var elem = $(formDom).find(':submit');
            elem.addClass("disabled");
            elem.data("default",elem.val());
            elem.val(text);
        },
        enable: function(formDom){
            var elem = $(formDom).find(':submit');
            elem.removeClass("disabled");
            elem.val(elem.data("default"));
        },
        loading : {
            show : function(formDom){
                $(formDom).find("#loading").show();
            },
            hide :function(formDom){
                $(formDom).find("#loading").hide();
            }
        }
    },
    
    clearInputs : function(formId){
        $("#"+formId).find('input').each(function(){
          $(this)[0].value = "";
        });
    },

    removeHiddenInputs : function(formId){
        $("#"+formId).find('input:hidden').each(function(){
          $(this).remove();
        });
    },
    confirmForm: function(formId, action){
        var formDom = document.getElementById(formId);
        form.submit.loading.show(formDom)
        var input = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "xsrftoken").val(utils.getCookie("xsrftoken"));
        $("#"+formId).append($(input));
        var alertClose = document.getElementById("alertClose");
        var myModalClose = document.getElementById("myModalClose");
        myModalClose.click();
        $("#"+formId).find("#confirm").val("true");
        var req = $.post( $("#"+formId)[0].action,
                          $("#"+formId).serialize(),
                        function (data){
                            if(data["success"]){
                                form.clearInputs(formId);
                                if(alertClose)
                                    alertClose.click();
                                if(data["data"]){
                                    var content = utils.getDataHtml(data["data"]);
                                    utils.prompt(action + " was successful. Your changes will take effect in 15 seconds." , content)
                                } else {
                                    utils.alert("success",action + " was successful. Your changes will take effect in 15 seconds");
                                }
                            } else {
                                utils.alert("warning", action + " failed. Reason: " + data["errors"][0]);
                            }
                  });
        req.fail(function(){
            utils.alert("alert","Request: "+action+" Failed.");
        });
        req.always(function(){
            form.removeHiddenInputs(formId);
            form.submit.enable(formDom);
            form.submit.loading.hide(formDom)
         });
    }
}