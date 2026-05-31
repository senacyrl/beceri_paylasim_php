body {

    background-color: rgb(253, 249, 239);

    font-family: Georgia, 'Times New Roman', Times, serif;
    margin: 0;
    padding: 0;
    color: rgb(56, 54, 58); /* Koyu gri/kahve metin tonu */
}

header {
    background-color: rgb(219, 214, 203);
    padding: 20px 5%;
    display: flex;
    justify-content: space-between; 
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

header h1 {
    margin: 0;
    color: purple; 
    font-size: 1.8rem;
    background: none;
}

nav {
    display: flex;
    gap: 20px;
}

nav a {
    text-decoration: none;
    color: black;
    font-weight: 550;
    transition: 0.3s;
}

nav a:hover {
    color: purple;
}

.container {
    max-width: 900px;
    margin: 40px auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.7); 
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

h2 {
    color: purple;
    font-size: 27px;
    border-bottom: 2px solid rgb(219, 214, 203);
    padding-bottom: 10px;
    margin-bottom: 20px;
}

.post {
    background-color: rgb(235, 230, 220);
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    border: 1px solid rgba(0,0,0,0.05);
    transition: transform 0.2s;
}

.post:hover {
    transform: translateY(-3px); 
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.post h3 {
    margin-top: 0;
    color: black;
}

.post p {
    font-size: 1.05rem;
    line-height: 1.6;
}

.post small {
    color: #666;
    font-style: italic;
    display: block;
    margin-top: 10px;
}

.button, .buton, button {
    display: inline-block;
    padding: 10px 20px;
    background-color: purple; 
    color: white !important;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: 0.3s;
}

.button:hover, .buton:hover, button:hover {
    background-color: #5e005e;
}

.btn-cancel {
    background-color: rgb(174, 170, 179) !important;
}

.profile-card {
    background-color: rgb(219, 214, 203);
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
}

#editSection {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 2px dashed purple;
    margin-top: 20px;
}

input, textarea {
    width: 100%;
    padding: 12px;
    margin-top: 8px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 8px;
    box-sizing: border-box;
    font-family: inherit;
}

input:focus, textarea:focus {
    border-color: purple;
    outline: none;
    box-shadow: 0 0 5px rgba(128, 0, 128, 0.2);
}

.success-msg {
    background-color: #d4edda;
    color: #155724;
    padding: 15px;
    border-radius: 8px;
    border-left: 5px solid purple; 
    margin-bottom: 20px;
}

hr {
    border: 0;
    border-top: 1px solid rgb(219, 214, 203);
    margin: 20px 0;
}



.button, .buton, button {
    display: inline-block;
    padding: 12px 24px;
    background-color: purple;
    color: white !important;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    
    min-width: 160px;               
    font-family: inherit;
    font-size: 15px;
}

.button:hover, .buton:hover, button:hover {
    background-color: #5e005e;     
    box-shadow: 0 4px 12px rgba(94, 0, 94, 0.25);
    transform: translateY(-1px);   
}

.btn-cancel {
    background-color: rgb(174, 170, 179) !important;
    color: white !important;
}

.btn-delete {
    color: #e74c3c;
    text-decoration: none;
    font-weight: bold;
    font-size: 0.85rem;
    float: right;
    padding: 5px 12px;
    border: 1px solid #e74c3c;
    border-radius: 6px;
    min-width: auto; 
    transition: 0.3s;
}

.btn-delete:hover {
    background-color: #e74c3c;
    color: white !important;
}
