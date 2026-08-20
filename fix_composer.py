import json

with open('/home/darta/loja-api/composer.json', 'r') as f:
    data = json.load(f)

# Reverter para as versões originais do projeto
data['require']['php'] = '^7.4.27'
data['require']['tymon/jwt-auth'] = '^1.0'

with open('/home/darta/loja-api/composer.json', 'w') as f:
    json.dump(data, f, indent=2)

print('COMPOSER_REVERTIDO')
