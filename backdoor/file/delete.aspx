<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	
	string filePath = root + "/" + path;
	if (!File.Exists(filePath)) {
		Response.Write(path + " is not a file");
		return;
	}
	
	File.Delete(filePath);
	Response.Write("delete [" + path + "]");
%>