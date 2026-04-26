<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>
<%@ Import Namespace="System.Web.Script.Serialization" %>
 
<% 
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("需要POST{path = (...)}");
		return;
	}
	
	// find dir
	string scanPath = Path.GetFullPath(path); // 去除多餘路徑
	if (!Directory.Exists(scanPath) ){
		Response.Write("該資料夾不存在: " + scanPath);
		return;
	}
	
	// scan dirs
	string[] dirs = Directory.GetDirectories(scanPath);
	for(int i = 0; i < dirs.Length; i++){
		dirs[i] = Path.GetFileName(dirs[i]);
	}
	
	// output json
	JavaScriptSerializer serializer = new JavaScriptSerializer();
	string json = serializer.Serialize(dirs);
	Response.Write(json);
	
	/*
	foreach (string dir in dirs){
		Response.Write(dir);
		Response.Write("<br>");
	}
	*/
%>