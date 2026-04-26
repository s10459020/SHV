<%@ Page Language="C#" CodeBehind="Default.aspx.cs" Inherits="YourNamespace.Default" %>
<!DOCTYPE html>
<html>
<head>
    <title>ASP.NET with Razor Example</title>
</head>
<body>
    <h1>ASP.NET with Razor Example</h1>

    <% if (ShowMessage) { %>
        <p><%= Message %></p>
    <% } %>

    <ul>
        <% foreach (var item in Items) { %>
            <li><%= item %></li>
        <% } %>
    </ul>
</body>
</html>