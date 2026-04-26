namespace YourNamespace
{
    public partial class Default : System.Web.UI.Page
    {
        protected bool ShowMessage { get; set; }
        protected string Message { get; set; }
        protected List<string> Items { get; set; }

        protected void Page_Load(object sender, EventArgs e)
        {
            ShowMessage = true;
            Message = "Welcome to the ASP.NET page!";
            Items = new List<string> { "Item 1", "Item 2", "Item 3" };
        }
    }
}