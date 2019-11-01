/*
Navicat MySQL Data Transfer

Source Server         : 20.10.1.51
Source Server Version : 50718
Source Host           : 20.10.1.51:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50718
File Encoding         : 65001

Date: 2019-11-01 11:01:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for game_audit
-- ----------------------------
DROP TABLE IF EXISTS `game_audit`;
CREATE TABLE `game_audit` (
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '平台',
  `bundleid` varchar(64) NOT NULL,
  `version` varchar(10) NOT NULL DEFAULT '' COMMENT '版本',
  PRIMARY KEY (`platform`,`bundleid`,`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of game_audit
-- ----------------------------
INSERT INTO `game_audit` VALUES ('appstore', 'com.manic.casino', '1.0.0');
INSERT INTO `game_audit` VALUES ('appstore', 'com.xxmanic.casino', '1.0.0');
INSERT INTO `game_audit` VALUES ('google', 'com.tencent.tmgp.paw', '1.0.0');
INSERT INTO `game_audit` VALUES ('google', 'com.tencent.tmgp.paw', '1.0.5');

-- ----------------------------
-- Table structure for yly_config_list
-- ----------------------------
DROP TABLE IF EXISTS `yly_config_list`;
CREATE TABLE `yly_config_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(64) DEFAULT NULL COMMENT '用户名',
  `value` text COMMENT '配置值',
  `tag` varchar(64) DEFAULT NULL COMMENT '标签',
  `create_time` int(20) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(29) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态：0 可用 1禁止',
  `ext1` varchar(512) DEFAULT NULL COMMENT '拓展1',
  `ext2` varchar(512) DEFAULT NULL COMMENT '拓展2',
  `ext3` varchar(512) DEFAULT NULL COMMENT '拓展3',
  `is_package` int(2) NOT NULL DEFAULT '0' COMMENT '0 全部包 1不是全部',
  `package` text NOT NULL COMMENT '选择的包',
  `is_config` int(2) NOT NULL DEFAULT '0' COMMENT '是否作用到config 0是 1否 ',
  `desc` int(11) NOT NULL DEFAULT '0' COMMENT '显示排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of yly_config_list
-- ----------------------------
INSERT INTO `yly_config_list` VALUES ('66', '华为', '1.0.22', 'hw_audit', '1568100549', '1568180651', '0', '1', '', '\"配置值\"为华为版本号  \"拓展1\"设置为1oooo 设置为0为xxx', '1', '{\"com.reallycattle.longzhu\":\"0\",\"com.reallycattle.globalql\":\"0\",\"com.longzhu.fish.us\":\"0\",\"com.longzhu.ninegame\":\"0\",\"com.longzhu.mega\":\"0\",\"com.renzhi.chu\":\"0\",\"com.longmen.nova\":\"0\",\"com.longmen.novaslots\":\"0\",\"com.ceshi.yong\":\"0\",\"com.ren.renzc\":\"0\",\"com.xxmanic.casino\":\"0\",\"com.manic.casino\":\"0\",\"com.ml.paw\":\"1\",\"com.tencent.tmgp.paw\":\"0\",\"com.reallycattle.tzmj\":\"0\"}', '1', '0');

-- ----------------------------
-- Procedure structure for update_achievement_data
-- ----------------------------
DROP PROCEDURE IF EXISTS `update_achievement_data`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `update_achievement_data`(gid int(4), gtype int(4),ghall int(4))
BEGIN
	 DECLARE uid BIGINT DEFAULT 0;
   DECLARE exeinfo VARCHAR(10240) DEFAULT '';
   DECLARE dbtime DATETIME DEFAULT '0000-00-00 00:00:00';
   DECLARE newpara VARCHAR(32) DEFAULT '';
   DECLARE len INT DEFAULT 0;
	 DECLARE res INT DEFAULT 0;
   DECLARE STOP int default 0;
	 DECLARE cur_kind CURSOR FOR SELECT id, t_tmp_sql_achievement.sql, stime FROM t_tmp_sql_achievement  WHERE game_id=gid AND game_type=gtype AND game_hallid=ghall AND state = -1 ORDER BY id LIMIT 1000;
	 DECLARE cur_all CURSOR FOR SELECT id, t_tmp_sql_achievement.sql, stime FROM t_tmp_sql_achievement  WHERE state = -1 ORDER BY id LIMIT 1000;
   DECLARE CONTINUE HANDLER FOR NOT FOUND SET STOP=1;

	 IF gid = -1 AND gtype=-1 AND ghall THEN
		 OPEN cur_all;
		 FETCH cur_all into uid,exeinfo,dbtime;
		 WHILE STOP <> 1 DO
				SET len = LENGTH(exeinfo);
				SET newpara = CONCAT(',','"',dbtime,'"',')');
				SET @stmt  = INSERT(exeinfo,len,1,newpara);
				PREPARE s1 from @stmt; 
				EXECUTE s1;
				DEALLOCATE PREPARE s1;
				SELECT @rel;
				IF @rel <> 0 THEN
					UPDATE t_tmp_sql_achievement SET state = 0 WHERE id=uid;
				ELSE
					-- UPDATE t_tmp_sql_achievement SET state = 1 WHERE id=uid;
					DELETE FROM t_tmp_sql_achievement WHERE id=uid;
				END IF;
				FETCH cur_all into uid,exeinfo,dbtime;
		 END WHILE;
		 CLOSE cur_all;
   ELSE
		 OPEN cur_kind;
		 FETCH cur_kind into uid,exeinfo,dbtime;
		 WHILE STOP <> 1 DO
				SET len = LENGTH(exeinfo);
				SET newpara = CONCAT(',','"',dbtime,'"',')');
				SET @stmt  = INSERT(exeinfo,len,1,newpara);
				PREPARE s1 from @stmt; 
				EXECUTE s1;
				DEALLOCATE PREPARE s1;
				SELECT @rel;
				IF @rel <> 0 THEN
					UPDATE t_tmp_sql_achievement SET state = 0 WHERE id=uid;
				ELSE
					-- UPDATE t_tmp_sql_achievement SET state = 1 WHERE id=uid;
					DELETE FROM t_tmp_sql_achievement WHERE id=uid;
				END IF;
				FETCH cur_kind into uid,exeinfo,dbtime;
		 END WHILE;
		 CLOSE cur_kind;
	 END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for userachievement_17
-- ----------------------------
DROP PROCEDURE IF EXISTS `userachievement_17`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `userachievement_17`(nuid INT(10),brtype INT(3),bridx INT(3),szinfo VARCHAR(1024),OUT nrel INT(10),IN `write_time` datetime)
BEGIN
	DECLARE nitems INT DEFAULT 0;
	DECLARE CONTINUE HANDLER FOR 1329 SET nrel=0;-- ????????
	-- ????id|???ұ仯|Ӯ??Ǯ|ˮλ|??ǰ????|????|??ע|˰|?????ʽ?|????ֵ|?ȼ?|????|net|bfcgm|band|lstinfo|id|rounds|logins(??????¼????)|usergmround|usergmp|usergmruntimep|newerrounds|szcontrollog
	SET nrel = 0;
	IF nuid > 0 THEN
		-- ?????ַ???
		SET nitems = func_split_TotalLength(szinfo,"|");
		IF nitems >= 27 THEN
			BEGIN
				DECLARE ntid INT DEFAULT 0;
				DECLARE nchange BIGINT DEFAULT 0;
				DECLARE nscore BIGINT DEFAULT 0;
				DECLARE nshuiwei BIGINT DEFAULT 0;
				DECLARE nremain BIGINT DEFAULT 0;
				DECLARE nbonus BIGINT DEFAULT 0;
				DECLARE nbet   BIGINT DEFAULT 0;
				DECLARE ntax BIGINT DEFAULT 0;
				DECLARE nlott BIGINT DEFAULT 0;
				DECLARE nexps BIGINT DEFAULT 0;
				DECLARE nlevel INT DEFAULT 0;
				DECLARE nstones INT DEFAULT 0;
				DECLARE ndbuid INT DEFAULT 0;
				DECLARE nmaxwins BIGINT DEFAULT 0;
				DECLARE nmaxlott BIGINT DEFAULT 0;
				DECLARE nmaxpop  BIGINT DEFAULT 0;
				DECLARE ndeltwin INT DEFAULT 0;
				DECLARE ntime INT DEFAULT 0;
				DECLARE nnet BIGINT DEFAULT 0;
				DECLARE bcfgm int default 0;
				DECLARE ngmrounds int default 0;
				DECLARE ndeltgmrounds int default 0;
				DECLARE band int default 0;
				DECLARE nid int default 0;
				DECLARE nleftrounds int default 0;
				DECLARE nlogins int default 0;
				DECLARE nlogingolds bigint default 0;
				DECLARE nmingolds	  bigint default 0;
				DECLARE nmaxgolds	  bigint default 0;
				DECLARE nkeyid	int default 0;
				DECLARE nkeyvalue int default 0;
				DECLARE ninrounds int default 0;
				DECLARE nusergmrounds int default 0;
				DECLARE nusergmp int default 0;
				DECLARE nusergmruntimep int default 0;
				DECLARE nnewerrounds int default 0;
				DECLARE szlstinfo varchar(8192) default '';
				DECLARE szcontrollog varchar(1024) default '';
				DECLARE szgmset VARCHAR(1024) DEFAULT '';
				SET ntid    		= func_split(szinfo,"|",1) + 0;
				SET nchange 		= func_split(szinfo,"|",2);
				SET nscore			= func_split(szinfo,"|",3);
				SET nshuiwei		= func_split(szinfo,"|",4);
				SET nremain 		= func_split(szinfo,"|",5);

				SET nbonus  		= func_split(szinfo,"|",6);
				SET nbet    		= func_split(szinfo,"|",7);
				SET ntax    		= func_split(szinfo,"|",8);
				SET nlott   		= func_split(szinfo,"|",9);
				SET nexps   		= func_split(szinfo,"|",10);

				SET nlevel  		= func_split(szinfo,"|",11);
				SET nstones			= func_split(szinfo,"|",12);
				SET nnet    		= func_split(szinfo,"|",13);
				SET bcfgm   		= func_split(szinfo,"|",14);
				SET band    		= func_split(szinfo,"|",15);

				SET szlstinfo 	= func_split(szinfo,"|",16);
				SET nid 	  		= func_split(szinfo,"|",17);
				SET nleftrounds = func_split(szinfo,"|",18);
				SET nlogins 		= func_split(szinfo,"|",19);
				set nlogingolds = func_split(szinfo,"|",20);

				set nmingolds 	= func_split(szinfo,"|",21);
				set nmaxgolds 	= func_split(szinfo,"|",22);
				set nkeyid 			= func_split(szinfo,"|",23);
				set nkeyvalue 	= func_split(szinfo,"|",24);
				set ninrounds 	= func_split(szinfo,"|",25);

				set nusergmrounds 	= func_split(szinfo,"|",26);
				set nusergmp 				= func_split(szinfo,"|",27);
				set nusergmruntimep = func_split(szinfo,"|",28);
				set nnewerrounds 		= func_split(szinfo,"|",29);
				SET szcontrollog 		= func_split(szinfo,"|",30);
				-- SET write_time      = func_split(szinfo,"|",31);
				SET ntime   =  (DATE_FORMAT(write_time,'%Y%m%d') + 0);
				if nnet < 0 then
					set nnet = 0;
				end if;
				-- log
				IF band = 0 THEN
					INSERT INTO log_user_golds (gameid,roomtype,roomidx,uid,ltime,changes,remaining,popcoins,state,remarks,trate) VALUES(17,brtype,bridx,nuid,NOW(),nchange,nremain,nbet,0,szcontrollog,0);
				END IF;
				-- ????table
				SELECT uid,max_wins,max_lott,roundmaxpop,gmrounds INTO ndbuid,nmaxwins,nmaxlott,nmaxpop,ngmrounds FROM user_achievement_17 WHERE uid = nuid;
				-- ???µ????ݿ?
				IF nchange > 0 THEN
					IF nchange > nmaxwins THEN
						SET nmaxwins = nchange;
					END IF;
				END IF;
				IF nscore > 0 THEN
					SET ndeltwin = 1;
				END IF;
				if ngmrounds > 0 and bcfgm&4 then
					set ndeltgmrounds = 1;
				end if;
				IF nbet > nmaxpop THEN
					SET nmaxpop = nbet;
				END IF;
				set szgmset = concat(nusergmrounds,'|',nusergmp,'|',nusergmruntimep);
				INSERT INTO user_achievement_17 (uid,game_changes,score,bet,rounds,wins,max_wins,roundmaxpop,exps,`level`,stones,s1,s2,s3,gmset,lstinfo) VALUES(nuid,nchange,nscore,nbet,1,ndeltwin,nmaxwins,nmaxpop,nexps,nlevel,nstones,nbet,nchange+nbet+nbonus,nbonus,szgmset,szlstinfo) ON DUPLICATE KEY UPDATE game_changes = game_changes + nchange,score=score+0,bet=bet+nbet,rounds = rounds + 1,wins = wins + ndeltwin,max_wins = nmaxwins,roundmaxpop = nmaxpop,exps = nexps,`level` = nlevel,stones = stones+nstones,s1 = s1 + nbet,s2 = s2 + nchange + nbet + nbonus,s3 = s3 + nbonus,gmrounds = gmrounds - ndeltgmrounds,gmset = szgmset,lstinfo = szlstinfo;
				SET ndbuid = 0;
				SELECT uid,maxpop INTO ndbuid,nmaxpop FROM log_user_liushui_17 WHERE ldate = ntime AND rtype = brtype AND uid = nuid;
				IF ndbuid = nuid THEN
					IF nbet > nmaxpop THEN
						SET nmaxpop = nbet;
					END IF;
					UPDATE log_user_liushui_17 SET changes = changes + nchange, scores = scores + nscore,shuiwei = shuiwei + nshuiwei,taxs = taxs + ntax,rounds = rounds + 1,wins = wins + ndeltwin,pops = pops + nbet, stones = stones + nstones,maxpop = nmaxpop,android = band,logingolds = nlogingolds,golds = nremain,mingolds = nmingolds,maxgolds = nmaxgolds,id = nkeyid,addp = nkeyvalue,inrounds = ninrounds WHERE ldate = ntime AND uid = nuid AND rtype = brtype;
				ELSE
					SET nmaxpop = nbet;
					INSERT INTO log_user_liushui_17(ldate,uid,rtype,changes,scores,stones,shuiwei,taxs,rounds,wins,pops,maxpop,android,logingolds,golds,mingolds,maxgolds,id,addp,inrounds) VALUES(ntime,nuid,brtype,nchange,nscore,nstones,nshuiwei,ntax,1,ndeltwin,nbet,nmaxpop,band,nlogingolds,nremain,nmingolds,nmaxgolds,nkeyid,nkeyvalue,ninrounds) ON DUPLICATE KEY UPDATE changes = changes + nchange, scores = scores + nscore,shuiwei = shuiwei + nshuiwei,taxs = taxs + ntax,rounds = rounds + 1,wins = wins + ndeltwin,pops = pops + nbet, stones = stones + nstones,maxpop = nmaxpop,android = band,logingolds = nlogingolds,golds = nremain,mingolds = nmingolds,maxgolds = nmaxgolds,id = nkeyid,addp = nkeyvalue,inrounds = ninrounds;
				END IF;
				-- ͳ???ܵ?
				INSERT INTO log_user_liushui_17(ldate,uid,rtype,changes,scores,stones,shuiwei,taxs,rounds,wins,pops) VALUES(ntime,0,brtype,nchange,nscore,nstones,nshuiwei,ntax,1,ndeltwin,nbet) ON DUPLICATE KEY UPDATE changes = changes + nchange,scores = scores + nscore,stones = stones + nstones,shuiwei = shuiwei + nshuiwei,taxs = taxs + ntax,rounds = rounds + 1,wins = wins + ndeltwin,pops = pops + nbet;
				update game_room_stat set bigchips = bigchips + nbet,chips = chips + nchange + nbet + nbonus + nlott,netchips = netchips + nnet where gameid = 17 and roomtype = brtype and roomidx = bridx;
				if nnet > 0 then
					insert into log_slots_net(rtype,ridx,net,ldate) values(brtype,bridx,nnet,now());
				end if;
				if band = 0 then
					INSERT INTO log_tax_stat (logtime,gameid,roomtype,roomidx,tableid,sysbosscoins) VALUES(ntime,17,brtype,bridx,0,-nchange) ON DUPLICATE KEY UPDATE `sysbosscoins`= `sysbosscoins` - nchange;
					if nbet > 0 then
						INSERT INTO log_game_room_stat(ldate,gameid,rtype,allbet) VALUES(ntime,17,brtype,nbet) ON DUPLICATE KEY UPDATE allbet = allbet + nbet;
					end if;
				end if;
				INSERT INTO game_user_lstdata (uid,gid,rtype,ridx,`type`,id,rounds) VALUES(nuid,17,brtype,bridx,0,nid,nleftrounds) ON DUPLICATE KEY UPDATE `id`= nid,rounds = nleftrounds;
				INSERT INTO game_user_lstdata (uid,gid,rtype,ridx,`type`,id,rounds) VALUES(nuid,17,brtype,bridx,1,5,nnewerrounds) ON DUPLICATE KEY UPDATE `id`= 5,rounds = nnewerrounds;
				if nlogins > 0 then
					update game_user_data_17 set logins = nlogins where uid = nuid;
				end if;
				-- nuid INT(10),nsavingchange BIGINT(20), ndelt BIGINT(20),nremain BIGINT(20),nallbonus BIGINT(20),nbonusround INT(3),ndelttime INT(10),nisbanker INT(3),npop BIGINT(20),ntax BIGINT(20),ntime INT(10),nprize INT(10),OUT nrel INT(10))
				CALL userachievement(nuid,0,nchange,nremain,0,0,0,0,nbet,ntax,ntime,0,band,@rel);
			END;
		ELSE
			SET nrel = -2;
		END IF;
	ELSE
		SET nrel = -1;
	END IF;
END
;;
DELIMITER ;

-- ----------------------------
-- Procedure structure for userachievement_9
-- ----------------------------
DROP PROCEDURE IF EXISTS `userachievement_9`;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `userachievement_9`(nuid INT(10),brtype INT(3),bridx INT(3),szinfo VARCHAR(1024),OUT nrel INT(10),write_time datetime)
BEGIN
	DECLARE nitems INT DEFAULT 0;
	DECLARE CONTINUE HANDLER FOR 1329 SET nrel=0;
	SET nrel = 0;
	IF nuid > 0 THEN			
		SET nitems = func_split_TotalLength(szinfo,"|");		
		IF nitems = 12 THEN
			BEGIN
				DECLARE nchange BIGINT DEFAULT 0;
				DECLARE nremain BIGINT DEFAULT 0;
				DECLARE ntime BIGINT DEFAULT 0;
				DECLARE nisbanker INT DEFAULT 0;
				DECLARE bisand INT DEFAULT 0;
				DECLARE ntaskbouns BIGINT DEFAULT 0;
				DECLARE nfivebouns BIGINT DEFAULT 0;
				DECLARE nroundmaxbet BIGINT DEFAULT 0;
				DECLARE nsavingchange BIGINT DEFAULT 0;
				DECLARE nwinchange BIGINT DEFAULT 0;
				DECLARE ndbgolds BIGINT DEFAULT 0;
				DECLARE nmaxgolds BIGINT DEFAULT 0;
					
				DECLARE ndbuid INT DEFAULT 0;
				DECLARE nmaxwins BIGINT DEFAULT 0;
				DECLARE taskbonusmax BIGINT DEFAULT 0;
				DECLARE taskbonusall BIGINT DEFAULT 0;
				DECLARE fivebonusmax BIGINT DEFAULT 0;
				DECLARE fivebonusall BIGINT DEFAULT 0;
				DECLARE taskbonusround INT DEFAULT 0;
				DECLARE fivebonusround INT DEFAULT 0;
				DECLARE nallbonus BIGINT DEFAULT 0;
				DECLARE nbonusround BIGINT DEFAULT 0;
				DECLARE ngamewin INT DEFAULT 0;
				DECLARE ngamedraw INT DEFAULT 0;
				DECLARE ngamelost INT DEFAULT 0;
				DECLARE nmaxbet BIGINT DEFAULT 0;
				DECLARE ntimenow BIGINT DEFAULT 0;
				DECLARE nsavingpot BIGINT DEFAULT 0;
				
				DECLARE szroomsinfo   			VARCHAR(512) DEFAULT '';
				DECLARE szroomsinfo1  			VARCHAR(512) DEFAULT '';
				DECLARE szroomsinfonew  		VARCHAR(512) DEFAULT '';
				DECLARE nItems 							INT(10) DEFAULT 0;
				DECLARE nItems1							INT(10) DEFAULT 0;
				DECLARE i										INT(10) DEFAULT 0;
				DECLARE bFind								INT(3)  DEFAULT 0;
				DECLARE nRoomType1					INT(3) DEFAULT 0;
				DECLARE nRoomRounds					INT(10) DEFAULT 0;
				DECLARE nRoomWins 					BIGINT(20) DEFAULT 0; 
				DECLARE nWin	 							INT(10) DEFAULT 0;
				DECLARE nstreak_win	 				INT(10) DEFAULT 0;
				DECLARE ntax 								BIGINT(20) DEFAULT 0; 
				DECLARE ntid 								INT(10) DEFAULT 0;
				DECLARE nlott 							BIGINT(20) DEFAULT 0; 
				DECLARE nsumwins 						BIGINT(20) DEFAULT 0; 
				DECLARE nBanker 						INT(10) DEFAULT 0;
				DECLARE nAnalyWin 					INT(10) DEFAULT 0;
				DECLARE szbaccinfo 					VARCHAR(8192) DEFAULT '';
				DECLARE ndeltabankeround 		INT(10) DEFAULT 0;
				DECLARE ndeltaplayeround 		INT(10) DEFAULT 0;
				
				-- 1tid|2???ұ仯|3Ŀǰ????|4 ????ʱ??|5?ʽ?????|6??|7?Ƿ???ׯ??|8??????ע|9?Ñ????X|10?Ƿ???????
				SET ntid								= func_split(szinfo,"|",1) + 0;
				SET nchange 						= func_split(szinfo,"|",2) + 0;
				SET nremain 						= func_split(szinfo,"|",3) + 0;
				SET ntime 							= func_split(szinfo,"|",4) + 0;
				SET nlott 							= func_split(szinfo,"|",5) + 0;
				SET ntax 								= func_split(szinfo,"|",6) + 0;
				SET nisbanker 					= func_split(szinfo,"|",7) + 0;
				SET nroundmaxbet 				= func_split(szinfo,"|",8) + 0;
				SET nsavingchange 			= func_split(szinfo,"|",9) + 0;
				SET bisand 							= func_split(szinfo,"|",10) + 0;
				SET ndeltabankeround		= func_split(szinfo,"|",11) + 0;
				SET ndeltaplayeround		= func_split(szinfo,"|",12) + 0;
			
				SELECT uid,max_wins,maxpop,streak_win,savingpot,roomsinfo,maxgolds INTO ndbuid,nmaxwins,nmaxbet,nstreak_win,nsavingpot,szroomsinfo,nmaxgolds FROM user_achievement_9 WHERE uid = nuid;
				
				SELECT golds INTO ndbgolds FROM game_userfield WHERE uid = nuid;
				 
				IF ndbgolds > nmaxgolds THEN 
					SET nmaxgolds = ndbgolds;
				END IF;
				IF nchange > 0 THEN
					IF nchange > nmaxwins THEN
						SET nmaxwins = nchange;
					END IF;
					SET nwinchange = nchange;
					SET ngamewin = 1;
					SET nWin = 1;
					SET nstreak_win =nstreak_win+1;
					SET nsumwins = nchange;
				ELSEIF nchange = 0 THEN
					SET ngamedraw = 1;
					SET nstreak_win=0;
				ELSE   
					
					SET ngamelost = 1;
					SET nstreak_win=0;
				
				END IF;
				-- ??Ǯ?޻??ֵ
				-- ????????666??
				IF (nsavingpot + nsavingchange)>=6666666 THEN 
					SET nsavingchange= 6666666 - nsavingpot;
				END IF;
				IF nsavingchange <0 THEN 
					SET nsavingchange =0;
				END IF;
				IF nroundmaxbet>nmaxbet THEN
					SET nmaxbet=nroundmaxbet;
				END IF;
				
				SET szroomsinfonew =szroomsinfo;
				IF nroundmaxbet > 0 THEN 
				SET nItems = func_split_TotalLength(szroomsinfo,"|");
				SET bFind = 0;
				SET szroomsinfonew = '';
				SET i = 0;
				
					while_label: WHILE nItems > 1 AND i < nItems DO
						SET i = i + 1;
						SET szroomsinfo1 = func_split(szroomsinfo,"|",i);
						SET nItems1 		 = func_split_TotalLength(szroomsinfo1,",");
						IF nItems1 = 3  THEN
							SET nRoomType1	= (func_split(szroomsinfo1,",",1) + 0); 
							SET nRoomRounds	= (func_split(szroomsinfo1,",",2) + 0); 
							SET nRoomWins 	= (func_split(szroomsinfo1,",",3) + 0); 
							IF nRoomType1 = brtype THEN 
								
							SET nRoomRounds = nRoomRounds + 1;
							IF nWin > 0 THEN
								SET nRoomWins = nRoomWins + 1;
							END IF;
							SET szroomsinfonew = CONCAT(szroomsinfonew,brtype,',',nRoomRounds,',',nRoomWins,'|');
							SET bFind = 1;
							ELSE
								SET szroomsinfonew = CONCAT(szroomsinfonew,szroomsinfo1,'|');
							END IF;
						END IF;
					END WHILE while_label;
					IF bFind < 1 AND nroundmaxbet > 0 THEN 
						IF nWin > 0 THEN
							SET nRoomWins = 1;
						ELSE
							SET nRoomWins = 0;
						END IF;
						SET szroomsinfonew = CONCAT(szroomsinfonew,brtype,',',1,',',nRoomWins,'|');
					END IF;
				END IF;
			
				IF ndbuid > 0 THEN
					IF ndbuid = nuid THEN
						UPDATE user_achievement_9 SET savingpot = savingpot + nsavingchange,game_changes = game_changes+ nchange,rounds = rounds + 1,wins = wins + ngamewin,draw = draw +  ngamedraw,lost = lost + ngamelost, max_wins = nmaxwins,upbosses = upbosses + nisbanker,maxpop = nmaxbet,game_time = game_time+ntime , roomsinfo = szroomsinfonew,streak_win=nstreak_win, allbet = allbet + nroundmaxbet,allwins = allwins +nwinchange,maxgolds = nmaxgolds,tax = tax + ntax WHERE uid = nuid;
					ELSE
						SET nrel = -3;
					END IF;
				ELSE
					INSERT INTO user_achievement_9 (uid,savingpot,game_changes,game_times,rounds,wins,draw,lost,max_wins,upbosses,maxpop,game_time,roomsinfo,streak_win,allbet,allwins,maxgolds,tax) 
					VALUES(nuid,nsavingchange,nchange,ntime,1,ngamewin,ngamedraw,ngamelost,nmaxwins,nisbanker,nroundmaxbet,ntime,szroomsinfonew,nstreak_win,nroundmaxbet,nwinchange,ndbgolds,ntax);
				END IF;
				IF nrel = 0 THEN
						SET ndbuid = 0;
						SET ntimenow = (DATE_FORMAT(write_time,'%Y%m%d') + 0);
				 		INSERT INTO log_user_liushui_9(ldate,uid,rtype,changes,savingchange,game_times,rounds,wins,sumwins,draw,lost,max_wins,upbosses,popall,maxpop,deltcharms,tax,android,bankround,playeround)  
				 			VALUES(ntimenow,nuid,brtype,(nchange+nlott),nsavingchange,ntime,1,ngamewin,nsumwins,ngamedraw,ngamelost,nmaxwins,nisbanker,nroundmaxbet,nroundmaxbet,0,ntax,bisand,ndeltabankeround,ndeltaplayeround)
							ON DUPLICATE KEY UPDATE savingchange = savingchange + nsavingchange,changes = changes + nchange + nlott,game_times = game_times+ntime,rounds = rounds + 1,wins = wins + ngamewin,sumwins = sumwins + nsumwins,draw = draw +  ngamedraw,lost = lost + ngamelost, max_wins = nmaxwins,upbosses = upbosses + nisbanker, popall=popall + nroundmaxbet,maxpop = nmaxbet ,tax = tax + ntax,android = bisand,bankround=bankround+ndeltabankeround,playeround=playeround+ndeltaplayeround;
						if nlott > 0 then
							insert into log_lottery(gid,rtype,ridx,tid,uid,win,`status`,ltime) values(9,brtype,bridx,ntid,nuid,nlott,1,now());
						end if;
						SET nallbonus = ntaskbouns + nfivebouns;
						CALL userachievement(nuid,nsavingchange,nchange,nremain,nallbonus,nbonusround,ntime,nisbanker,nroundmaxbet,0,ntimenow,0,bisand,@rel);
				END IF;
			END;
		ELSE
			SET nrel = -2;
		END IF;
	ELSE
		SET nrel = -1;
	END IF;
END
;;
DELIMITER ;
