SET FOREIGN_KEY_CHECKS=0;
truncate parent_link;
SET FOREIGN_KEY_CHECKS=1;


INSERT into parent_link (link) VALUES('http://mstag.wired.com/2003/11/moovl/') , ('http://mstag.wired.com/2003/11/more_global_dip/');

select * from parent_link;