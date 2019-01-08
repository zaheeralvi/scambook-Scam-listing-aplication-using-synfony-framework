
$(document).ready(function () {
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(1)  a i.fa.fa-folder").addClass('fa-user');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(2)  a i.fa.fa-folder").addClass('fa-users');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(3)  a i.fa.fa-folder").addClass('fa-trophy');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(4)  a i.fa.fa-folder").addClass('fa-location-arrow');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(5)  a i.fa.fa-folder").addClass('fa-bell-o');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(6)  a i.fa.fa-folder").addClass('fa-bullhorn');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(7)  a i.fa.fa-folder").addClass('fa-list');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(1)  a i.fa.fa-user").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(2)  a i.fa.fa-users").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(3)  a i.fa.fa-trophy").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(4)  a i.fa.fa-location-arrow").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(5)  a i.fa.fa-bell-o").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(6)  a i.fa.fa-bullhorn").removeClass('fa-folder');
    $(".sidebar ul.sidebar-menu li.treeview:nth-child(7)  a i.fa.fa-list").removeClass('fa-folder');
    
    $("a.confirm-delete").click(function(e) {
        e.preventDefault();
        var $link = $(this);
        
        if (confirm("Are you sure?") == true) {
            document.location.assign($link.attr('data-href'));
        }
//        bootbox.confirm("Are you sure?", function(result) {
//                if (result == true) {
//                    document.location.assign($link.attr('data-href'));
//                }
//            });
        });
});
