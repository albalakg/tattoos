<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goldens -Application Error</title>
    
    <style>
        table {
            width: 80%;
            margin: auto;
            direction: ltr;
        }

        td {
            min-width: 100px;
        }
    </style>
</head>


<body>
    <table>
        <thead>
            <tr>
                <td align="center">
                    
                </td>
                <td align="center">
                    <h1>
                        Application Error
                    </h1>
                </td>
                <td align="center">
                   
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="left">
                    <p>
                        Error Message: 
                    </p>
                </td>
                <td align="left">
                    <p>
                        {{
                            $data->getMessage()
                        }}
                    </p>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <p>
                        Error File: 
                    </p>
                </td>
                <td align="left">
                    <p>
                        {{
                            $data->getFile()
                        }}
                    </p>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <p>
                        Error Line: 
                    </p>
                </td>
                <td align="left">
                    <p>
                        {{
                            $data->getLine()
                        }}
                    </p>
                </td>
            </tr>
            <tr>
                <td align="left">
                    <p>
                        Error Stack: 
                    </p>
                </td>
                <td align="left">
                    <p>
                        {{
                            $data->__toString()
                        }}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>