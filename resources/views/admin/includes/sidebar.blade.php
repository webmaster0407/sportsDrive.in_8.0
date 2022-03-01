<!-- sidebar menu: : style can be found in sidebar.less -->
<?php $data = getCurrentUrl()?>
<ul class="sidebar-menu">
    <li class="header"></li>
    <li class="treeview <?php if ($data == "administrator/home" || $data == "administrator/site-settings" || $data == "administrator/change-password") {
	echo 'active';
}
?>">
        <a href="#">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="<?php if ($data == "administrator/home") {
	echo 'active';
}
?>" ><a href="/administrator/home"><i class="fa fa-circle-o"></i> Dashboard</a></li>
            <li class="<?php if ($data == "administrator/site-settings") {
	echo 'active';
}
?>" ><a href="/administrator/site-settings"><i class="fa fa-cog"></i> Site Settings</a></li>
            <li class="<?php if ($data == "administrator/change-password") {
	echo 'active';
}
?>" ><a href="/administrator/change-password"><i class="fa fa-square"></i> Change Password</a></li>
        </ul>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-pages") {
	echo 'active';
}
?>">
        <a href="/administrator/list-pages">
            <i class="fa fa-files-o"></i>
            <span>CMS Pages</span>
        </a>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-banners") {
	echo 'active';
}
?>">
        <a href="/administrator/list-banners">
            <i class="fa fa-picture-o"></i>
            <span>Banners Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-offers") {
	echo 'active';
}
?>">
        <a href="/administrator/list-offers">
            <i class="fa fa-cube"></i>
            <span>Offers Management</span>
        </a>
    </li>
     <li class="treeview <?php if ($data == "administrator/list-coupons") {
	echo 'active';
}
?>">
        <a href="/administrator/list-coupons">
            <i class="fa fa-bell-o"></i>
            <span>Coupons Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-customer") { echo 'active'; } ?>">
        <a href="/administrator/list-customer">
            <i class="fa fa-users"></i>
            <span>Customers Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-partner") { echo 'active'; } ?>">
        <a href="/administrator/list-partner">
            <i class="fa fa-users"></i>
            <span>Partners Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-slots") { echo 'active'; } ?>">
        <a href="/administrator/list-slots">
            <i class="fa fa-users"></i>
            <span>Slots Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-customer-groups") { echo 'active'; } ?>">
        <a href="/administrator/list-customer-groups">
            <i class="fa fa-users"></i>
            <span>Customers Group Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-promotions" || $data == "administrator/list-attributes-groups") { echo 'active';}?>">
        <a href="#">
            <i class="fa fa-filter"></i>
            <span>Promotions Management</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="<?php if ($data == "administrator/list-product-promotions") { echo 'active';}?>">
                <a href="/administrator/list-product-promotions"><i class="fa fa-share"></i>List Product Promotions</a></li>
            <li class="<?php if ($data == "administrator/list-coupon-promotions") {echo 'active';}?>">
                <a href="/administrator/list-coupon-promotions"><i class="fa fa-share"></i>List Coupon Promotions</a></li>
        </ul>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-brand") { echo 'active'; } ?>">
        <a href="/administrator/list-brand">
            <i class="fa fa-tag"></i>
            <span>Brands Management</span>
        </a>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-categories") {
	echo 'active';
}
?>">
        <a href="/administrator/list-categories">
            <i class="fa fa-sitemap"></i>
            <span>Categories Management</span>
        </a>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-attributes" || $data == "administrator/list-attributes-groups") {
	echo 'active';
}
?>">
        <a href="#">
            <i class="fa fa-filter"></i>
            <span>Attributes Management</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="<?php if ($data == "administrator/list-attributes-groups") {
	echo 'active';
}
?>" ><a href="/administrator/list-attributes-groups"><i class="fa fa-share"></i>List Attribute Groups</a></li>
            <li class="<?php if ($data == "administrator/list-attributes") {
	echo 'active';
}
?>" ><a href="/administrator/list-attributes"><i class="fa fa-share"></i>List Attributes</a></li>
        </ul>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-products") {
	echo 'active';
}
?>">
        <a href="/administrator/list-products">
            <i class="fa fa-cube"></i>
            <span>Products Management</span>
        </a>
    </li>
    <li class="treeview <?php if ($data == "administrator/list-orders") {
	echo 'active';
}
?>">
        <a href="/administrator/list-orders">
            <i class="fa  fa-cubes"></i>
            <span>Orders Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-visitors") { echo 'active'; } ?>">
        <a href="/administrator/list-visitors">
            <i class="fa fa-users"></i>
            <span>Visitors Management</span>
        </a>
    </li>

    <li class="treeview <?php if ($data == "administrator/list-otps") { echo 'active'; } ?>">
        <a href="/administrator/list-otps">
            <i class="fa fa-users"></i>
            <span>Customers OTP's</span>
        </a>
    </li>
    <li class="treeview">
        <a href="/administrator/logout">
            <i class="fa fa-sign-out"></i>
            <span>Logout</span>
        </a>
    </li>
</ul>