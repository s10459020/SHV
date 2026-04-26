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
	
	string name = Path.GetFileName(filePath);
	HttpContext.Current.Response.Clear();
	HttpContext.Current.Response.ContentType = "application/octet-stream";
	HttpContext.Current.Response.AddHeader("Content-Disposition", "attachment; filename=\"" + name + "\"");
	HttpContext.Current.Response.TransmitFile(filePath);
	HttpContext.Current.Response.End();
%>