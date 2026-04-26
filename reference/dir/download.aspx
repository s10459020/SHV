<%@ Page Language="C#" Debug="true" %>
<%@ Import Namespace="System.IO" %>
<%@ Import Namespace="System.IO.Compression" %>
<%@ Import Namespace="System.Web.Hosting" %>

<script runat="server">
const string Message = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
const string OriginalFileName = "E:/Projects/VHC/htdocs/api/original.txt";
const string CompressedFileName = "E:/Projects/VHC/htdocs/api/compressed.dfl";
const string DecompressedFileName = "E:/Projects/VHC/htdocs/api/decompressed.txt";

void Page_Load(object sender, EventArgs e){
	string root = HostingEnvironment.MapPath("~");
	string path = Request.QueryString["path"];
	if (path == null){
		Response.Write("need POST{path}!");
		return;
	}
	
	string dirPath = root + "/" + path;
	string zipFile = root + "/" + "api/output.zip";
	if (!Directory.Exists(dirPath)) {
		Response.Write(path + " is not a dir");
		return;
	}
	
    CompressDirectory(dirPath, zipFile);
}

void CompressDirectory(string sourceDirectory, string zipFile)
{
	using (FileStream zipFileStream = new FileStream(zipFile, FileMode.Create))
	{
		using (GZipStream zipStream = new GZipStream(zipFileStream, CompressionMode.Compress))
		{
			DirectoryInfo directoryInfo = new DirectoryInfo(sourceDirectory);
			FileInfo[] files = directoryInfo.GetFiles();

			foreach (FileInfo file in files)
			{
				CompressFile(file.FullName, file.Name, zipStream);
			}
		}
	}
}

void CompressFile(string sourceFile, string entryName, Stream zipStream)
{
	byte[] buffer = new byte[4096];

	using (FileStream fileStream = new FileStream(sourceFile, FileMode.Open, FileAccess.Read))
	{
		zipStream.Write(BitConverter.GetBytes((int)fileStream.Length), 0, 4);
		zipStream.Write(BitConverter.GetBytes((int)entryName.Length), 0, 4);
		zipStream.Write(Encoding.Default.GetBytes(entryName), 0, entryName.Length);

		int bytesRead;
		while ((bytesRead = fileStream.Read(buffer, 0, buffer.Length)) > 0)
		{
			zipStream.Write(buffer, 0, bytesRead);
		}
	}
}
</script>
