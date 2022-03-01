
$(document).ready(function () {

    var params={page:[1]};
    var preSearch=location.search;
    var searchParams=preSearch.substr(1,preSearch.length);
    var baseUrl= window.location.origin+window.location.pathname;
    if(preSearch){
        if(searchParams.indexOf('&')>-1){
            var p=searchParams.split('&');
            p.forEach(function(param){
                var uv=param.split('=');
                uv[1]&&(params[uv[0]]=uv[1].indexOf(',')>-1?uv[1].split(','):[uv[1]]);
            })
        }
        initFilter();
    }

    function initFilter(){
        var k=Object.keys(params);
        k.forEach(function(key){
            var wrap=$('div[data-filter-type="'+key+'"]');
            if(wrap.length){
                var selected=params[key];
                selected.forEach(function(s){
                    $('input[value="'+s+'"]',wrap).length&&($('input[value="'+s+'"]',wrap)[0].checked=true);
                })
            }
        })
    }

    function applyFilter(el){
        var that=$(el),group=that.parents('#catlogFilters'),label=that.parents('.filter')[0].dataset.filterType;
        var checkes=$('input:checked',group);
        var filterVal=[];
        checkes.each(function(){filterVal.push(this.value)});
        if(filterVal.length){
            params[label]=filterVal;
        }else{delete params[label]}
        var url=createUrl();
        $("#filter_url").val(url);
        filter();
    }

    function addSortBy(val){
        var filterVal=[];
        filterVal.push(val);
        var label="sortBy";
        if(filterVal.length){
            params[label]=filterVal;
        }else{delete params[label]}
        var url = createUrl();
        $("#filter_url").val(url);
        filter();
    }


    function addResultPerPage(val){
        var filterVal=[];
        filterVal.push(val);
        var label="pp";
        if(filterVal.length){
            params[label]=filterVal;
        }else{delete params[label]}
        var url = createUrl();
        $("#filter_url").val(url);
        filter();
    }

    function createUrl(){
        var k=Object.keys(params);
        var str="";
        var mappping=[]
        k.forEach(function(key){
            mappping.push(key+"="+params[key].join(','))
        })
        return baseUrl+'?'+mappping.join('&')
    }


    $(document).on('change', '.categories', function() {
        applyFilter(this);
    });

    $('#sortBy').change(function () {
        var val = $(this).val();
        addSortBy(val);
    });

    $('#per_page_result').change(function () {
        var val = $(this).val();
        addResultPerPage(val);
    });

    $(document).on("click", '.select-cat li i', function(event) {
        event.preventDefault();
        var val = $(this).attr("data-id");
        $("#"+val).attr("checked", false).change();
    });


    $(document).on("click", 'ul.pagination a', function(event) {
        event.preventDefault();
        var  filter_url = $("#filter_url").val();
        var url = $(this).attr('href');
        var nextPage = url.split('page=')[1];
        var filterval = filter_url.replace(/,\s*$/, "");
        var token = $('input[id=token]').val();
        var product_ids = $('#product_ids').val();
        String.prototype.replaceAt=function(index, replacement) {
            return this.substr(0, index) + replacement+ this.substr(index + replacement.length);
        };
        var n = filter_url.indexOf("page=");
        var finalUrl = "";
        if(n != -1)
             finalUrl = filter_url.replaceAt(n,"page="+nextPage);
        else
             finalUrl = filter_url+"&page="+nextPage;
        $.ajax({
            type: "POST",
            dataType: "json",
            headers: {'X-CSRF-TOKEN': token},
            url: "/search/filter-search",
            data: {"filterPara": filterval,"product_ids":product_ids,"page":nextPage},
            success: function (data) {
                if(data['productData']==null){
                    $(".pagination").replaceWith(data['links']);
                    $("#prod_list").hide();
                    $("#empty_list").show();
                }else{
                    if(data['paginationCount']=="0"){
                        $(".pagination").show();
                        $(".pagination").replaceWith(data['links']);
                    }else{
                        $(".pagination").hide();
                    }
                    $("#empty_list").hide();
                    $("#prod_list").show();
                    $("#prod_list").html(data['productData']);
                }
                window.history.pushState('obj', 'Sports Drive-Product Listing', finalUrl);
                $("#filter_url").val(finalUrl);
            }
        });

    });


    function filter() {
        var filterval = $("#filter_url").val();
        filterval = filterval.replace(/,\s*$/, "");
        var token = $('input[id=token]').val();
        var product_ids = $('#product_ids').val();
        $.ajax({
            type: "POST",
            dataType: "json",
            headers: {'X-CSRF-TOKEN': token},
            url: "/search/filter-search",
            async: false,
            data: {"filterPara": filterval,"product_ids":product_ids},
            success: function (data) {
                if(data['productData']==null){
                    $(".pagination").hide();
                    $("#prod_list").hide();
                    $("#empty_list").show();
                }else{
                    if(data['paginationCount']=="0"){
                        $(".pagination").show();
                        $(".pagination").replaceWith(data['links']);
                    }else{
                        $(".pagination").hide();
                    }
                    $("#empty_list").hide();
                    $("#prod_list").show();
                    $("#prod_list").html(data['productData']);
                }
                $("#selected_filters").html(data['selected']);
                window.history.pushState('obj', 'Sports Drive-Product Listing', filterval);
            }
        });
    }


});

