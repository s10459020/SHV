<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<%
	string root = HostingEnvironment.MapPath("~");
	string dir = Request.Form["dir"];
	if (dir == null){
		Response.Write("need POST{dir}!");
		return;
	}
	
	int i = 1;
	string name = "new-file";
	while(File.Exists(root+"/"+dir+"/"+name)){
		i++;
		name = "new-file(" + i + ")";
	}
	
	using (File.Create(root+"/"+dir+"/"+name)) { }
	Response.Write("create ["+dir+"/"+name+"]");
%>