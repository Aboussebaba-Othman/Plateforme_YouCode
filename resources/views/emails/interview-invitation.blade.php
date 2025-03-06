<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4a6cf7;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px 30px;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .details {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border-left: 4px solid #4a6cf7;
        }
        .interview-type {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 3px;
            font-size: 14px;
            color: white;
            background-color: #4a6cf7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Convocation à l'Entretien</h1>
        </div>
        
        <div class="content">
            <p>Cher(e) {{ $interview->candidate->name }},</p>
            
            <p>Nous avons le plaisir de vous convoquer à un entretien dans le cadre de votre candidature à YouCode.</p>
            
            <div class="details">
                <h2>Détails de l'entretien</h2>
                <p>
                    <strong>Type :</strong> 
                    <span class="interview-type">{{ $interview->type_label }}</span>
                </p>
                <p><strong>Date :</strong> {{ $interview->formatted_date }}</p>
                <p><strong>Heure :</strong> {{ $interview->formatted_time }}</p>
                <p><strong>Lieu :</strong> {{ $interview->location }}</p>
                <p><strong>Examinateur :</strong> {{ $interview->staff->name }}</p>
            </div>
            
            <p>Veuillez vous présenter 10 minutes avant l'heure prévue muni(e) des documents suivants :</p>
            
            <ul>
                <li>Pièce d'identité</li>
                <li>CV à jour</li>
                <li>Copies de vos diplômes et certificats</li>
            </ul>
            
            <p>Si vous avez des questions ou si vous ne pouvez pas vous présenter à la date prévue, veuillez nous contacter dès que possible par email à <a href="mailto:contact@youcode.ma">contact@youcode.ma</a> ou par téléphone au 05XXXXXXXX.</p>
            
            <p>Nous vous souhaitons bonne chance pour votre entretien.</p>
            
            <p>
                Cordialement,<br>
                L'équipe YouCode
            </p>
        </div>
        
        <div class="footer">
            <p>Ceci est un email automatique, merci de ne pas y répondre directement.</p>
            <p>© {{ date('Y') }} YouCode - Tous droits réservés</p>
        </div>
    </div>
</body>
</html>