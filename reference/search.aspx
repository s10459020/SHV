<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Script.Serialization" %>
 
<% 
	string scanPath = "C:\\inetpub\\wwwroot";
	
	// scan dirs
	string[] dirs = Directory.GetDirectories(scanPath);
	foreach (string dir in dirs){
		Response.Write(dir);
		Response.Write("<br>");
	}
	
	string[] files = Directory.GetFiles(scanPath);
	foreach (string file in files){
		Response.Write(file);
		Response.Write("<br>");
	}
%>