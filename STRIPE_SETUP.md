# Configuracion de Stripe  
  
## Obtener Credenciales  
  
1. Ve a https://dashboard.stripe.com/register  
2. Crea cuenta en modo prueba  
3. Ve a Developers  keys  
4. Copia tus claves  
  
## Configurar  
  
Edita .env:  
STRIPE_KEY=pk_test_xxx  
STRIPE_SECRET=sk_test_xxx  
  
## Probar  
  
Tarjeta: 4242 4242 4242 4242  
Fecha: cualquier fecha futura  
CVC: 123  
  
## Produccion  
  
Cambia a claves live cuando estes listo 
