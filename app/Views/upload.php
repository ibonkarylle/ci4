<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>


    <form action="<?= base_url('/sendEmail') ?>" method="post">
        <button type="submit">Send</button>
    </form>

</body>

</html>
<style>
form {
    display: flex;
    flex-direction: column;
    height: 100vh;
    width: 100%;
}
</style>