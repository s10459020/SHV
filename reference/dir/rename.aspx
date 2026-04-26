<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<%
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	string name = Request.Form["name"];
	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	if (name == null){
		Response.Write("need POST{name}!");
		return;
	}
	
	string filePath = root + "/" + path;
	if (!Directory.Exists(filePath)) {
		Response.Write(filePath + "is not a dir");
		return;
	}
	
	string dir = Path.GetDirectoryName(filePath);
	string newPath = dir + "/" + name;
	if (Directory.Exists(newPath)) {
		Response.Write("dir already exist");
		return;
	}
	
	Directory.Move(filePath, newPath);
	Response.Write("rename ["+filePath+"/] to ["+newPath+"/]");
%>