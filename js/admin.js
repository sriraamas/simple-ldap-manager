$(window).load(function() {
    $("#adPassForm").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.resetPwd(event);
        }
        event.preventDefault();
    });

    $("#adminVpn").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.revokeVpn(event);
        }
        event.preventDefault();
    });

    $("#adminSsh").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.revokeSsh(event);
        }
        event.preventDefault();
    });

    $("#newUser").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.addUser(event);
        }
        event.preventDefault();
    });

    $("#user_search").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.userSearch(event);
        }
        event.preventDefault();
    });

    $("#group_search").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.groupSearch(event);
        }
        event.preventDefault();
    });

    $("#availability").click( function(event){
        if($("#newUser").find("#uname")[0].value != ""){
            handlers.admin.checkAvailability(event);
        }
        event.preventDefault();
    });

    $("#newUser").on("change",function(event){
        if($(this).find("#uname")[0].value != ""){
            handlers.admin.checkAvailability(event)
        }
        event.preventDefault();
    });

    $('#myTabs').on('toggled', function (event, tab) {
        utils.hideAll();
     });

    $(document).on('closed.fndtn.reveal', '[data-reveal]', function () {
        var curFormDom = $(document).find("section.active").find("form");
        form.removeHiddenInputs(curFormDom[0].id);
        form.submit.loading.hide(curFormDom);
        form.submit.enable(curFormDom);
        $("#avlbl").empty();
        var alertClose = document.getElementById("alertClose");
        if(alertClose)
            alertClose.click();
    });
});




var handlers = {
     admin : {
        resetPwd : function(event){
            var formDom = event.target;
            form.submit.loading.show(formDom);
            form.submit.disable(formDom,"Resetting Password");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize(), handlers.admin.action.resetPwd.verify);
            req.fail(function(){
                utils.alert("alert","Request to Reset Password Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });

        },
        revokeVpn: function(event){
            var formDom = event.target;
            var input = $("<input>")
                           .attr("type", "hidden")
                           .attr("name", "property").val("VPN");
            $(formDom).append($(input));
            form.submit.disable(formDom,"Revoking VPN");
            form.submit.loading.show(formDom)
            var req = $.post( $(formDom)[0].action,$(formDom).serialize(), handlers.admin.action.revokeVpn.verify);
            req.fail(function(){
                utils.alert("alert","Request to Revoke VPN Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
                form.removeHiddenInputs(formDom.id);
            });
        },
        revokeSsh: function(event){
            var formDom = event.target;
            var input = $("<input>")
                           .attr("type", "hidden")
                           .attr("name", "property").val("SSH");
            $(formDom).append($(input));
            form.submit.loading.show(formDom)
            form.submit.disable(formDom,"Revoking SSH");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize(), handlers.admin.action.revokeSsh.verify);
            req.fail(function(){
                utils.alert("alert","Request to Revoke SSH Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
                form.removeHiddenInputs(formDom.id);
            });
        },
        addUser: function(event){
            var formDom = event.target;
            form.submit.disable(formDom,"Creating User");
            form.submit.loading.show(formDom);
            var req = $.post("admin/getGroups.php",$(formDom).serialize(), handlers.admin.action.addUser.verify);
            req.fail(function(){
                utils.alert("alert","Request to Create User Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
                form.removeHiddenInputs(formDom.id);
            });
        },
        userSearch: function(event){
            var formDom = event.target;
            form.submit.disable(formDom,"Searching");
            form.submit.loading.show(formDom);
            $("#avlbl").empty();
            var req = $.post( $(formDom)[0].action,$(formDom).serialize(), handlers.admin.action.userSearch.confirm);
            req.fail(function(){
                utils.alert("alert","Search Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });
        },
        groupSearch: function(event){
            var formDom = event.target;
            form.submit.disable(formDom,"Searching");
            form.submit.loading.show(formDom);
            $("#avlbl").empty();
            var req = $.post( $(formDom)[0].action,$(formDom).serialize(), handlers.admin.action.groupSearch.confirm);
            req.fail(function(){
                utils.alert("alert","Search Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });
            
        },
        action : {
            resetPwd : {
                verify: function(data) { 
                    if(data['success']){
                        var confirmButton = "<br><button id='cnfButton' onclick='handlers.admin.action.resetPwd.confirm()' value='Confirm'> Confirm Password Reset</button>";
                        var input1 = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "dn").val(data["data"]["dn"]);
                        $("#adPassForm").append($(input1));
                        utils.prompt("Are you sure, you want to reset password for the following user?", utils.getInfoHtml(data["data"]) + confirmButton)
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        var formDom = document.getElementById("resetPwd");
                        form.submit.enable(formDom);
                        form.submit.loading.hide(formDom);
                    }
                },
                confirm: function(){
                    form.confirmForm("adPassForm", "Reset Password");
                }
            },
            revokeVpn : {
                verify: function(data) {
                    if(data['success']){
                        var confirmButton = "<br><button id='cnfButton' onclick='handlers.admin.action.revokeVpn.confirm()' value='Confirm'> Confirm VPN Revoke</button>";
                        var input1 = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "dn").val(data["data"]["dn"]);
                        $("#adminVpn").append($(input1));
                        utils.prompt("Are you sure, you want to revoke VPN Credentials for the following user?", utils.getInfoHtml(data["data"]) + confirmButton)
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        var formDom = document.getElementById("adminVpn");
                        form.submit.enable(formDom);
                        form.submit.loading.hide(formDom);
                    }
                },
                confirm: function(){
                    form.confirmForm("adminVpn", "Revoke VPN Credentials");
                }
              },
            revokeSsh : {
                verify: function(data) { 
                    if(data['success']){
                        var confirmButton = "<br><button id='cnfButton' onclick='handlers.admin.action.revokeSsh.confirm()' value='Confirm'> Confirm SSH Revoke</button>";
                         var input1 = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "dn").val(data["data"]["dn"]);
                        $("#adminSsh").append($(input1));
                     
                        utils.prompt("Are you sure, you want to revoke SSH Credentials for the following user?", utils.getInfoHtml(data["data"]) + confirmButton)
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        var formDom = document.getElementById("adminSsh");
                        form.submit.enable(formDom);
                        form.submit.loading.hide(formDom);
                    }
                },
                confirm: function(){
                    form.confirmForm("adminSsh", "Revoke SSH Credentials");
                }
            },
            addUser: {
                verify: function (data){
                    if(data["success"]){
                        var doneButton = "<br><button  type='button' onclick='handlers.admin.action.addUser.confirm()'>Done</button>";
                        utils.prompt("Please select the group to add the new user",utils.getGroupFormHtml(data["data"])+ doneButton);
                    } else {
                        utils.alert("alert",data["errors"][0]);
                         var formDom = document.getElementById("newUser");
                        form.submit.enable(formDom);
                        form.submit.loading.hide(formDom);
                    }
                },
                confirm: function(){
                    $("#groupSelect").find("input:checked").each(function(){
                      var input = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "groups[]").val($(this)[0].value);
                      $("#newUser").append($(input));
                    });
                    var confirmButton = "<br><button id='submitButton'  type='button' > Confirm</button>";
                    utils.updatePrompt("Kindly verify the following details of the new user.", utils.getNewUserInfoHtml() + confirmButton)
                    $("#submitButton").click(function() { form.confirmForm("newUser","Create New User")});
                }
            },
            userSearch:{
                confirm: function(data){
                    var alertClose = document.getElementById("alertClose");
                    if(alertClose)
                        alertClose.click();
                    if(data["success"]){
                         $("#searchResult").html("<table class='tdisplay dataTable stripe' id='table_id'></table>");
                         $("#table_id").dataTable({
                            "data" : data["data"],
                            "columns": [
                                {"title" : "First Name", "data" : "givenName"},
                                {"title" : "Middle Name", "data" : "middleName"},
                                {"title" : "Last Name", "data" : "sn"},
                                {"title": "Username","data" : "sAMAccountName"},
                                {"title": "Home Phone","data" : "homePhone"},
                                {"title": "Mobile","data" : "mobile"},
                                {"title": "Account Status","data" : "userAccountControl", "render": function(data){
                                        if(data == "Enabled"){
                                            return "<span style='color:green'>" + data.toUpperCase() + "</span>";
                                        } else
                                            if (data.indexOf("Disabled") > -1){
                                                return "<span style='color:red'>" + data.toUpperCase() + "</span>";
                                            } else {
                                                return "<span style='color:orange'>" + data.toUpperCase() + "</span>";
                                            }
                                    }
                                },
                                {"title" : "Groups", "data":"memberOf", "render": function ( data, type, full, meta ) {
                                        if(data.length > 0){
                                            var str = "<ul>";
                                            for(var i=0;i<data.length;i++){
                                                str += "<li><span class='text-center label radius' >" + data[i]+ "</span></li>";
                                            }
                                            str += "</ul>";
                                            return str;
                                        } else {
                                            return "-";
                                        }
                                    }
                                }
                            ]
                        })
                        $(document).foundation('accordion', 'reflow');
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        $("#searchResult").html("");
                    }
                    var formDom = $("section.active").find("form")
                    form.submit.enable(formDom);
                    form.submit.loading.hide(formDom);
                }
            },
            groupSearch:{
                confirm: function(data){
                    var alertClose = document.getElementById("alertClose");
                    if(alertClose)
                        alertClose.click();
                    if(data["success"]){
                         $("#searchResult").html("<table class='tdisplay dataTable stripe' id='table_id'></table>");
                         $("#table_id").dataTable({
                            "data" : data["data"],
                            "columns" : [
                                {"title": "Group Name","data" : "sAMAccountName"},
                                {"title" : "Users", "render": function(data,type,row,meta){
                                        var groupName = row["sAMAccountName"];
                                       return "<ul class='accordion userAccordion' data-accordion><li class='accordion-navigation'  id='"+ groupName+"'><a href = '#"+groupName+"Users' >Users</a><div id='"+groupName+"Users' class='content userC'></div></li></ul>" ;
                                    }
                                }
                            ]
                        })
                        $(document).foundation('accordion', 'reflow');
                        $('.userAccordion').on('toggled', function (event, accordion) {
                           console.log(event);
                           if(!accordion.data("users")) {
                                $(accordion).html("<img src='img/loading.gif' class='loading' id='loading' />");
                                var encId = encodeURIComponent(accordion.parent()[0].id);
                                $.post("admin/getUsersInGroup.php",
                                $("section.active form").find("#aname,#apwd").serialize() +"&uname="+encId,
                                function(data){
                                    var str ;
                                    var users = data["data"];
                                    if(users.length > 0) {
                                        str = "<ul>";
                                        for (var i=0; i< users.length;i++){
                                            str += "<li><span class='text-center label radius' >";
                                            str += users[i];
                                            str += "</span></li>";
                                        }
                                    } else {
                                        str = "No Users";
                                    }
                                    $(accordion).html(str);
                                    accordion.data("users",true);
                                });
                           }

                        });
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        $("#searchResult").html("");
                    }
                    var formDom = $("section.active").find("form")
                    form.submit.enable(formDom);
                    form.submit.loading.hide(formDom);
                }
            }
        },
        checkAvailability : function(){
            var noAdminName = ($("#newUser").find('#aname').val() == "")
            var noAdminPwd = ($("#newUser").find('#apwd').val() == "")
            var noUserName = ($("#newUser").find('#uname').val() == "")
            if ( !noAdminName && !noAdminPwd && !noUserName){
                $.post("admin/check_username.php",
                     $("#newUser").find("#aname, #apwd, #uname").serialize(),
                    function (data){
                        if (data["success"]){
                            var username =  $("#newUser").find('#uname').val()
                            if(data["data"]){
                                $("#avlbl").html(username + " is available");
                                $("#avlbl").css("color","green");
                            } else {
                                $("#avlbl").html(username + " is not available");
                                $("#avlbl").css("color","red");
                            }   
                        } else {
                            $("#avlbl").html("Error Occured!");
                            $("#avlbl").css("color","red");
                        }
                });
            }
        }
    }   
}
