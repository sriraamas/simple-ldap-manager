$(window).load(function() {
    $("#adPassForm").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.resetPwd(event);
        }
        event.preventDefault();
    });

    $("#manageUser").submit( function(event){
        if(form.canSubmit(this)){
            handlers.admin.manageUser(event);
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
            var token = utils.getCookie("xsrftoken");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() + "&xsrftoken="+token , handlers.admin.action.resetPwd.verify);
            req.fail(function(){
                utils.alert("alert","Request to Reset Password Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
            });

        },

        manageUser: function(event){
            var formDom = event.target
            form.submit.disable(formDom,"Managing");
            form.submit.loading.show(formDom)
            var token = utils.getCookie("xsrftoken");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() + "&xsrftoken="+token, handlers.admin.action.manageUser.verify);
            req.fail(function(){
                utils.alert("alert","Request to Manage User Failed");
                form.submit.enable(formDom);
                form.submit.loading.hide(formDom);
                form.removeHiddenInputs(formDom.id);
            });
        },
        addUser: function(event){
            var formDom = event.target;
            form.submit.disable(formDom,"Creating User");
            form.submit.loading.show(formDom);
            var token = utils.getCookie("xsrftoken");
            var req = $.post("admin/getGroups.php",$(formDom).serialize() + "&xsrftoken="+token, handlers.admin.action.addUser.verify);
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
            var token = utils.getCookie("xsrftoken");
            var req = $.post( "/admin/userSearch.php",$(formDom).serialize() + "&xsrftoken="+token, handlers.admin.action.userSearch.confirm);
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
            var token = utils.getCookie("xsrftoken");
            var req = $.post( $(formDom)[0].action,$(formDom).serialize() + "&xsrftoken="+token, handlers.admin.action.groupSearch.confirm);
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
            manageUser: {
                verify: function(data){
                    if(data['success']){
                        var button1 = "<br><button id='button1' onclick='handlers.admin.action.manageUser.confirm(this)' value='revokeVPN'> Revoke VPN</button>";
                        var button2 = " <button id='button2' onclick='handlers.admin.action.manageUser.confirm(this)' value='revokeSSH'> Revoke SSH</button>";
                        var button3 = " <button id='button3' onclick='handlers.admin.action.manageUser.confirm(this)' value='disable'> Disable</button>";
                        var button4 = " <button id='button4' onclick='handlers.admin.action.manageUser.confirm(this)' value='enable'> Enable</button>";
                        var confirmButton = button1 + button2 + button3 + button4;
                        var input1 = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "dn").val(data["data"]["dn"]);
                        var formDom = $("section.active form")
                        $(formDom).append($(input1));
                        $(formDom).data("data", data["data"]);
                        utils.prompt("Please choose the desired action.", utils.getInfoHtml(data["data"]) + confirmButton)
                    } else {
                        utils.alert("warning",data["errors"][0]);
                        var formDom = $("section.active form")
                        form.submit.enable(formDom);
                        form.submit.loading.hide(formDom);
                    }
                },
                confirm : function(buttonObj){
                    var input2 = $("<input>")
                               .attr("type", "hidden")
                               .attr("name", "userAction").val(buttonObj.value)
                    var formDom = $("section.active form")
                    $(formDom).append($(input2));
                    var cnfButton = "<br><button id='cnfButton' onclick='handlers.admin.action.manageUser.confirm2(\""+buttonObj.innerHTML+"\")' value='"+buttonObj.value+"'> "+buttonObj.innerHTML+"</button>";
                    switch(buttonObj.value){
                        case "revokeVPN": 
                                    utils.updatePrompt("Are you sure you want to Revoke VPN Credentials for the following user", utils.getInfoHtml($(formDom).data("data")) + cnfButton )
                                    break;
                        case "revokeSSH":
                                    utils.updatePrompt("Are you sure you want to Revoke SSH Credentials for the following user", utils.getInfoHtml($(formDom).data("data")) + cnfButton )
                                    break;
                        case "disable":
                                    utils.updatePrompt("Are you sure you want to Disable the following user", utils.getInfoHtml($(formDom).data("data")) + cnfButton )
                                    break;
                        case "enable":
                                    utils.updatePrompt("Are you sure you want to Enable the following user", utils.getInfoHtml($(formDom).data("data")) + cnfButton )
                                    break;
                    }
                },
                confirm2 : function(action){
                    var formDom = $("section.active form")
                    var name = $(formDom).data("data")["name"];
                    $(formDom).attr("action","/admin/manageUser.php")
                    form.confirmForm($(formDom).attr('id'), action + " of \"" + name +"\"");
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
                confirm: function(data) {
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
                                },
                                { "title": "Manage", "data":"sAMAccountName", "render": function (data,type,full,meta){
                                        return "<button type='button' class='label tiny radius mybutton' onclick='handlers.admin.action.userSearch.manageUser(\""+data+"\")'> Manage</button>";
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
                },
                manageUser: function(uname){
                    var token = utils.getCookie("xsrftoken");
                    var uName = $('section.active form #uname');
                    if (uName.val()){
                        uName.val(uname);
                    } else {
                         var input = $("<input>")
                                   .attr("id","uname")
                                   .attr("type", "hidden")
                                   .attr("name", "uname").val(uname);
                         $("#user_search").append($(input));
                    }
                    var req = $.post( "/admin/manageUser.php",$("#user_search").serialize() + "&xsrftoken="+token, handlers.admin.action.manageUser.verify);
                    req.fail(function(){
                        utils.alert("alert","Request to Manage User Failed");
                        form.removeHiddenInputs(formDom.id);
                    });
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
                                var token = utils.getCookie("xsrftoken");
                                $.post("/admin/getUsersInGroup.php",
                                $("section.active form").find("#aname,#apwd").serialize() +"&uname="+encId + "&xsrftoken=" + token,
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
                var token = utils.getCookie("xsrftoken");
                $.post("/admin/checkUsername.php",
                     $("#newUser").find("#aname, #apwd, #uname,#xsrftoken").serialize() + "&xsrftoken="+token,
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
