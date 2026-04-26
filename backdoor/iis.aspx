<%@ Page Language="C#" %>
<%@ Import Namespace="Microsoft.Web.Administration" %>

<% 
try{
	using (ServerManager serverManager = new ServerManager())
	{
		// 讀取所有站台
		SiteCollection sites = serverManager.Sites;

		foreach (Site site in sites)
		{
			Response.Write("站台名稱: " + site.Name);
			Response.Write("物理路徑: " + site.Applications["/"].VirtualDirectories["/"].PhysicalPath);
			Response.Write("綁定資訊:");
			foreach (Binding binding in site.Bindings)
			{
				Response.Write(" - " + binding.Protocol + "://" + binding.Host + ":" + binding.EndPoint.Port);
			}
			Response.Write();
		}
	}
} catch(Exception e){
	Response.Write("發生了一個例外：" + e.Message);
}
%>