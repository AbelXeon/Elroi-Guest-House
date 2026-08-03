<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Elroi Guest House</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
 <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">    <style>
        :root{
            --ink:#1c2b29;
            --panel:#20342f;
            --accent:#3f6b52;
            --accent-light:#57876a;
            --bg:#f6f5f2;
            --border:#e0ded7;
            --text:#26312e;
            --muted:#6b756f;
            --error:#b3413a;
            --error-bg:#fbeceb;
        }

        *{ margin:0; padding:0; box-sizing:border-box; }
        html,body{ height:100%; }

        body{
            font-family:'Inter', sans-serif;
            background:var(--bg);
            color:var(--text);
            display:flex;
            min-height:100vh;
        }

        /* ---- Left brand panel ---- */
        .brand-panel{
            flex:0 0 42%;
            background:linear-gradient(160deg, var(--ink) 0%, var(--panel) 100%);
            color:#f4f2ec;
            display:flex;
            flex-direction:column;
            justify-content:space-between;
            padding:48px;
        }

        .brand-mark{
            display:flex;
            align-items:center;
            gap:12px;
        }
        .brand-mark svg{ flex-shrink:0; }
        .brand-mark span{
            font-family:'Bebas Neue', sans-serif;
            font-weight:400;
            font-size:22px;
            letter-spacing:0.03em;
        }

        .brand-copy h1{
            font-family:'Bebas Neue', sans-serif;
            font-weight:400;
            font-size:clamp(34px, 3.5vw, 44px);
            line-height:1.15;
            letter-spacing:0.5px;
            max-width:380px;
            margin-bottom:16px;
        }
        .brand-copy p{
            font-size:14.5px;
            line-height:1.6;
            color:#c7cec8;
            max-width:340px;
        }

        .brand-footer{
            font-size:12px;
            color:#8b978f;
            letter-spacing:0.02em;
        }

        /* ---- Right form panel ---- */
        .form-panel{
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:32px;
        }

        .form-wrap{
            width:100%;
            max-width:360px;
        }

        .mobile-brand{
            display:none;
        }

        .form-wrap h2{
            font-family:'Bebas Neue', sans-serif;
            font-weight:400;
            font-size:30px;
            letter-spacing:0.5px;
            margin-bottom:6px;
        }
        .form-wrap .subtext{
            font-size:13.5px;
            color:var(--muted);
            margin-bottom:32px;
        }

        .field{
            position:relative;
            margin-bottom:22px;
        }
        .field input{
            width:100%;
            padding:16px 14px 8px;
            font-family:'Inter', sans-serif;
            font-size:15px;
            color:var(--text);
            background:#fff;
            border:1px solid var(--border);
            border-radius:8px;
            outline:none;
            transition:border-color .15s ease;
        }
        .field input::placeholder{ color:transparent; }
        .field input:focus{ border-color:var(--accent); }

        .field label{
            position:absolute;
            left:14px;
            top:16px;
            font-size:15px;
            color:var(--muted);
            pointer-events:none;
            transition:top .15s ease, font-size .15s ease, color .15s ease;
            background:transparent;
        }

        .field input:focus + label,
        .field input:not(:placeholder-shown) + label{
            top:6px;
            font-size:11px;
            color:var(--accent);
            letter-spacing:0.03em;
        }

        button{
            width:100%;
            padding:14px;
            margin-top:6px;
            background:var(--accent);
            border:none;
            border-radius:8px;
            color:#fff;
            font-family:'Inter', sans-serif;
            font-weight:600;
            font-size:14.5px;
            cursor:pointer;
            transition:background .15s ease;
        }
        button:hover{ background:var(--accent-light); }

        .error{
            margin-top:16px;
            padding:11px 14px;
            border-radius:7px;
            background:var(--error-bg);
            border:1px solid #f0c7c4;
            color:var(--error);
            font-size:13px;
        }

        .form-footer{
            margin-top:28px;
            font-size:12px;
            color:var(--muted);
            text-align:center;
        }

        /* ---- Mobile ---- */
        @media (max-width:860px){
            body{ flex-direction:column; }

            .brand-panel{ display:none; }

            .mobile-brand{
                display:flex;
                align-items:center;
                gap:10px;
                padding:24px 24px 0;
            }
            .mobile-brand span{
                font-family:'Bebas Neue', sans-serif;
                font-weight:400;
                font-size:22px;
                letter-spacing:0.03em;
                color:var(--ink);
            }

            .form-panel{
                align-items:flex-start;
                padding-top:12px;
            }

            .form-wrap{ max-width:100%; margin:0 auto; padding:0 4px; }
        }

        @media (max-width:400px){
            .form-panel{ padding:20px; }
        }
    </style>
</head>
<body>

    <div class="brand-panel">
        <div class="brand-mark">
            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="30" rx="7" fill="#3f6b52"/>
                <path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
            </svg>
            <span>Elroi Guest House</span>
        </div>

        <div class="brand-copy">
            <h1>Manage rooms, guests, and stays from one dashboard.</h1>
            <p>Sign in with the username and password provided to you by the front desk administrator.</p>
        </div>

        <div class="brand-footer">Hawassa, Ethiopia</div>
    </div>

    <div class="form-panel">
        <div class="form-wrap">

            <div class="mobile-brand">
                <svg width="26" height="26" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="30" height="30" rx="7" fill="#3f6b52"/>
                    <path d="M8 20V13.5L15 8L22 13.5V20H17V15.5H13V20H8Z" fill="#f4f2ec"/>
                </svg>
                <span>Elroi Guest House</span>
            </div>

            <h2>Welcome back</h2>
            <p class="subtext">Enter your credentials to access the dashboard.</p>

            <form method="POST" action="/login">
                @csrf

                <div class="field">
                    <input type="text" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>

                <div class="field">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>

                <button type="submit">Sign In</button>

                @if($errors->any())
                    <div class="error">{{ $errors->first() }}</div>
                @endif
            </form>

            <div class="form-footer">Elroi Guest House — Staff &amp; Admin Portal</div>
        </div>
    </div>

</body>
</html>