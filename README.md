# region
淘宝四级地址库

包括省、市、区/县、街道/镇

数据来源：<br /> 
中华人民共和国国家统计局<br /> 
行政区划代码<br /> 
统计用区划和城乡划分代码<br /> 

在上述基础数据上有所加工，为了和淘宝（菜鸟）四级地址库保持完全一致。



技术说明：
1. 包含shell script一份region.sh，依赖mysql mysql-config-editor 工具，为了不再脚本中显示出现代码
参考文章：
http://dev.mysql.com/doc/refman/5.6/en/mysql-config-editor.html
http://stackoverflow.com/questions/20751352/suppress-warning-messages-using-mysql-from-within-terminal-but-password-written

<br /> 
参考命令：请换成自己mysql目录
/usr/local/mysql/bin/mysql_config_editor set --login-path=client --host=127.0.0.1 --port=3306 --user=beta --password
<br /> 
/usr/local/mysql/bin/mysql_config_editor print --all

2. region.sql 一份，可以直接导入你的数据库
<br /> 
CREATE TABLE `region` (<br /> 
  `region_id` int(10) unsigned NOT NULL AUTO_INCREMENT,<br /> 
  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '父区域id',<br /> 
  `region_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '区域名称',<br /> 
  `region_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '区域类型，0-中国、1-省、2-市、3-区、4-街道',<br /> 
  PRIMARY KEY (`region_id`),<br /> 
  KEY `parent_id` (`parent_id`) USING BTREE,<br /> 
  KEY `region_type` (`region_type`) USING BTREE<br /> 
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='region区域表';<br /> 





