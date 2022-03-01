<!-- sidebar menu: : style can be found in sidebar.less -->
<?php $data = getCurrentUrl()?>
<ul class="sidebar-menu">
    <li class="header"></li>
    <li class="treeview <?php if ($data == "partner/home" || $data == "partner/site-settings" || $data == "partner/change-password") {echo 'active'; } ?>">
        <a href="#">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="<?php if ($data == "partner/home") { echo 'active'; }?>" >
                <a href="/partner/home"><i class="fa fa-circle-o"></i> Dashboard</a></li>
            <li class="<?php if ($data == "partner/change-password") { echo 'active'; } ?>" >
                <a href="/partner/change-password"><i class="fa fa-square"></i> Change Password</a></li>
        </ul>
    </li>

    <li class="treeview">
        <a href="/partner/logout">
            <i class="fa fa-sign-out"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>