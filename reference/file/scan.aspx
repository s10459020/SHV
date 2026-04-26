<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>
<%@ Import Namespace="System.Web.Script.Serialization" %>
 
<% 
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("需要POST{path = (...)}");
		return;
	}
	
	// find dir
	string fullPath = root + path;
	string scanPath = Path.GetFullPath(fullPath); // 去除多餘路徑
	if (!Directory.Exists(scanPath) ){
		Response.Write("該資料夾不存在: " + scanPath);
		return;
	}
	
	// scan files
	string[] files = Directory.GetFiles(scanPath);
	for(int i = 0; i < files.Length; i++){
		files[i] = Path.GetFileName(files[i]);
	}
	
	// output json
	JavaScriptSerializer serializer = new JavaScriptSerializer();
	string json = serializer.Serialize(files);
	Response.Write(json);
	
	/*
	foreach (string file in files){
		Response.Write(file);
		Response.Write("<br>");
	}
	*/
%>