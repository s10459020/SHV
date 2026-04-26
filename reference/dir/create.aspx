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
	
	// new-folder(n)
	int i = 1;
	string name = "new-folder";
	while(Directory.Exists(root+"/"+dir+"/"+name)){
		i++;
		name = "new-folder(" + i + ")";
	}
	
	Directory.CreateDirectory(root+"/"+dir+"/"+name);
	Response.Write("create ["+dir+"/"+name+"/]");
%>