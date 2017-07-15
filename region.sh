#!/bin/bash

#mac egrep -i -w \["ordersShortInfo",'[0-9]{1,9}'\] | egrep -v '\[\]'
#debian egrep '\["ordersShortInfo",\w*\]'

##http://dev.mysql.com/doc/refman/5.6/en/mysql-config-editor.html
#http://stackoverflow.com/questions/20751352/suppress-warning-messages-using-mysql-from-within-terminal-but-password-written
#/usr/local/mysql/bin/mysql_config_editor set --login-path=client --host=127.0.0.1 --port=3306 --user=wjzhu --password
#/usr/local/mysql/bin/mysql_config_editor print --all

rootpath=/Users/beta/www.stats.gov.cn
region=region
street=street
region_list_html=index.html
region_list_tmp=region_list_tmp

region_url=http://www.stats.gov.cn/tjsj/tjbz/xzqhdm/
street_url=http://www.stats.gov.cn/tjsj/tjbz/tjyqhdmhcxhfdm/

denominator=100
sleep_time=0.01
zero=0
default_parent_id=1
default_year=2016
province_denominator=10000
city_denominator=100
dot_html=.html
decoding=decoding

get_parent_region_id () {
    local region_id=$1
    local quotient=$1
    local parent_id=$1
    local count=0
    while [ "$quotient" != "$zero" ] 
    do
        count=`expr $count + 1`
        remainder=`expr $quotient % $denominator`
        quotient=`expr $quotient / $denominator`
        if [ "$remainder" != "$zero" ] ; then
            break;
        fi
    done

    local factor=1
    for(( i=0; i< $count; ++i))
    do
        factor=`expr $factor \* $denominator`
    done

    parent_id=`expr $quotient \* $factor`
    if [ "$parent_id" = "$zero" ]; then
        parent_id=$default_parent_id
    fi

    echo $parent_id
}

get_region_type () {
    local quotient=$1
    local count=0
    while [ "$quotient" != "$zero" ] 
    do
        count=`expr $count + 1`
        remainder=`expr $quotient % $denominator`
        quotient=`expr $quotient / $denominator`
        if [ "$remainder" != "$zero" ] ; then
            break;
        fi
    done

    local region_type=`expr $count - 4`
    region_type=`expr -1 \* $region_type`
    echo $region_type
}

check_dir () {
    cd $rootpath
    mkdir -p $region
    mkdir -p $street
}

get_region_list_url () {
    local ret_url=$region_url
    echo "$ret_url" 
}

get_region_detail_url () {
	local year_month=$1
    local ret_url=$region_url$year_month
    echo "$ret_url"
}

get_street_list_url () {
    local year=$1
    local district_id=$2
    local pid=`expr $district_id / $province_denominator`
    local cid=`expr $district_id % $province_denominator`
    cid=`expr $cid / $city_denominator`
    cid=`printf "%.2d" $cid`
    local ret_url=$street_url$year/$pid/$cid/$district_id$dot_html
    echo "$ret_url"
}

update_street () {
    local district_id=$1
    local district_region_type=$2

    local tmp_street_list_dir=$rootpath/$street/$district_id
    mkdir -p $tmp_street_list_dir

    local tmp_street_list_html=$tmp_street_list_dir/$district_id$dot_html

    if [ ! -f "$tmp_street_list_html" ]; then
        street_list_url=`get_street_list_url $default_year $district_id`
        echo "wget $street_list_url -O $tmp_street_list_html > /dev/null 2>&1"
        wget $street_list_url -O $tmp_street_list_html > /dev/null 2>&1
        sleep $sleep_time
    fi

    local tmp_street_list_html_decoding=$tmp_street_list_dir/$district_id$dot_html$decoding
    local tmp_street_list_path=$tmp_street_list_dir/$district_id
    iconv -f CP936 -t UTF-8 $tmp_street_list_html  > $tmp_street_list_html_decoding
    cat $tmp_street_list_html_decoding | grep -oP '(?<=<tr).*?(?=</td></tr>)' > $tmp_street_list_path

    local street_count=`cat $tmp_street_list_path | wc -l`
    echo $street_count
    for i in $(seq $street_count)
    do
        local tmp_sedp=`echo $i | sed 's/\(.*\)/\1p/'`
        local tmp_street_item=`cat $tmp_street_list_path | sed -n $tmp_sedp`
        local region_id=`echo $tmp_street_item | grep -oP "(?<=.html'>).*?(?=</a>)" | sed -n 1p`
        region_id=`expr $region_id / 1000`
        local region_name=`echo $tmp_street_item | grep -oP "(?<=.html'>).*?(?=</a>)" | sed -n 2p | sed 's/办事处//g'`
        region_name="\"`echo $region_name`\""
        local parent_id=$district_id
        local region_type=`expr $district_region_type + 1`
        echo "INSERT IGNORE INTO merchant.region (region_id, parent_id, region_name, region_type) VALUES ($region_id, $parent_id, $region_name, $region_type);"
        /usr/local/mysql/bin/mysql --login-path=client --host=127.0.0.1 -e "
            SET NAMES 'utf8';
            INSERT IGNORE INTO merchant.region (region_id, parent_id, region_name, region_type) VALUES ($region_id, $parent_id, $region_name, $region_type);
        "
    done
}



update_region () {
    local tmp_region_list_html=$rootpath/$region/$region_list_html

    if [ ! -f "$tmp_region_list_html" ]; then
        region_list_url=`get_region_list_url`
        echo "wget $region_list_url -O $tmp_region_list_html > /dev/null 2>&1"
        wget $region_list_url -O $tmp_region_list_html > /dev/null 2>&1
        sleep $sleep_time
    fi

    local latest_region=`cat $tmp_region_list_html | tr -d '\r\n' | grep -oP '(?<=<div class="center_list">).*?(?=</div>)' | grep -oP '(?<=<li).*?(?=<li>)' | head -1 | grep -oP '(?<=href=").*?(?=")'`
    local lastet_region_html=`echo "${latest_region##*/}"`
    local tmp_region_detail_html=$rootpath/$region/$lastet_region_html

    if [ ! -f "$tmp_region_detail_html" ]; then
        region_detail_url=`get_region_detail_url $latest_region`
        echo "wget $region_detail_url -O $tmp_region_detail_html > /dev/null 2>&1"
        wget $region_detail_url -O $tmp_region_detail_html > /dev/null 2>&1
        sleep $sleep_time
    fi

    local tmp_region_list_path=$rootpath/$region/$region_list_tmp
    cat $tmp_region_detail_html | tr -d '\r\n' | grep -oP '(?<=<p class="MsoNormal">).*?(?=</p>)' > $tmp_region_list_path
    local region_count=`cat $tmp_region_list_path | wc -l`
    echo $region_count 
    for i in $(seq $region_count) 
    do
        local tmp_sedp=`echo $i | sed 's/\(.*\)/\1p/'`
        local tmp_region_item=`cat $tmp_region_list_path | sed -n $tmp_sedp`
        local region_id=`echo $tmp_region_item | grep -oP '(?<=<span lang="EN-US">).*?(?=<span>)'`
        local region_name=`echo $tmp_region_item | grep -oP '(?<=<span style="font-family: 宋体">).*?(?=</span>)' | tr -d '\r\n'`
        region_name="\"`echo $region_name`\""
        local parent_id=`get_parent_region_id $region_id`
        local region_type=`get_region_type $region_id`
        echo "INSERT IGNORE INTO merchant.region (region_id, parent_id, region_name, region_type) VALUES ($region_id, $parent_id, $region_name, $region_type);"
        /usr/local/mysql/bin/mysql --login-path=client --host=127.0.0.1 -e "
            SET NAMES 'utf8';
            INSERT IGNORE INTO merchant.region (region_id, parent_id, region_name, region_type) VALUES ($region_id, $parent_id, $region_name, $region_type);
        "

        update_street $region_id $region_type
    done
}

grab_to_host () {
	check_dir
	update_region
}

echo "`date +'%Y-%m-%d %H:%M:%S'` [region] start"

## test
grab_to_host

echo "`date +'%Y-%m-%d %H:%M:%S'` [region] stop"
