<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
    Response.ContentType = "text/html; charset=utf-8";
    Response.ContentEncoding = System.Text.Encoding.UTF8;
	
	// get data
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("需要POST{path = (...)}");
		return;
	}
	
	// find dir
	string fullPath = root + path;
	string scanPath = Path.GetFullPath(fullPath);
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