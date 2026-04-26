<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string path = Request.Form["path"];
	string scanPath = Path.GetFullPath(path);
	
	bool isFile = File.Exists(scanPath);
	bool isDir = Directory.Exists(scanPath);
	if (!isFile && !isDir){
		Response.Write("該物件不存在: " + scanPath);
		return;
	}
	
	if (isDir){
		Response.Write("dir");
		return;
	} else {
		Response.Write("file");
		return;
	}
%>