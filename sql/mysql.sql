CREATE TABLE spotlight (
  sid int(5) unsigned NOT NULL default '0',
  item int(5) unsigned NOT NULL default '1',
  auto int(5) unsigned NOT NULL default '0',
  catid int(5) unsigned NOT NULL default '0',
  auto_cat int(5) unsigned NOT NULL default '1',
  module varchar(25) NOT NULL default 'news',
  image varchar(50) NOT NULL default 'spotlight.png',
  auto_image int(5) unsigned NOT NULL default '0',
  image_align varchar(10) NOT NULL default ''
) TYPE=MyISAM;

INSERT INTO spotlight (sid,item,auto,catid,auto_cat,module,image,auto_image,image_align) VALUES (1,1,1,0,1,'news','spotlight.png',1,'L');
INSERT INTO spotlight (sid,item,auto,catid,auto_cat,module,image,auto_image,image_align) VALUES (2,1,1,0,1,'wfsection','spotlight.png',1,'L');


