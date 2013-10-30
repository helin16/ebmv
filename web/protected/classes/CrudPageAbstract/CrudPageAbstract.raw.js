var CrudPageJs=new Class.create();CrudPageJs.prototype=Object.extend(new AdminPageJs(),{
	pagination: {pageNo: 1, pageSize: 30} //this is the pagination for the crud page
});