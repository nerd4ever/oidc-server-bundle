# Biblioteca do Nerd4ever OIDC Server Bundle

A biblioteca do Nerd4ever OIDC Server Bundle é um componente que fornece suporte ao OpenID Connect (OIDC) no Symfony,
através de um bundle. Essa biblioteca foi desenvolvida para facilitar a configuração e simplificar o uso do OpenID
Connect em projetos que já utilizam o oauth2-server-bundle e oauth2-server.

## Recursos e Objetivos

- Integração com o oauth2-server-bundle: A biblioteca se integra perfeitamente com o oauth2-server-bundle e aproveita
  suas configurações existentes. Isso permite adicionar suporte ao OpenID Connect ao seu projeto com o mínimo de
  esforço.

- Simplificação da configuração: A biblioteca visa simplificar a configuração do OpenID Connect, fornecendo arquivos de
  configuração mais simples e uma API intuitiva. Isso reduz a complexidade e a curva de aprendizado necessárias para
  implementar o OpenID Connect.

- Alto grau de acoplamento: A biblioteca é altamente acoplada aos projetos oauth2-server-bundle e oauth2-server, o que
  permite uma integração estreita e um uso mais eficiente das funcionalidades desses projetos

## Principais recursos

- Suporte ao OpenID Connect: A biblioteca oferece suporte ao OpenID Connect, incluindo a geração de
  tokens de acesso, autenticação de usuários, validação de tokens, gerenciamento de sessões e muito mais.
- Configuração simplificada: A biblioteca fornece arquivos de configuração simples e claros para configurar o OpenID
  Connect no Symfony.
- Extensibilidade e personalização: A biblioteca foi projetada para ser extensível e permite personalizar e estender
  suas funcionalidades de acordo com as necessidades do projeto.

## Como usar

- Instalação: A biblioteca pode ser instalada via Composer. Basta adicionar a dependência ao seu arquivo composer.json e
  executar o comando composer install:

````
composer require nerd4ever/oidc-server-bundle
````

- Configuração: A configuração do OpenID Connect pode ser feita através de arquivos do configuração simples, fornecidos
  pela biblioteca normalmente disponível em **config/packages/nerd4ever_oidc_server.yaml**. Esses arquivos contêm
  informações como classe de session, se desejar usar sua própria classe, provedor de identidade para fazer a validação
  de usuários e a definição da persistência, algo semelhante ao exemplo abaixo:

````
nerd4ever_oidc_server:
    session:
        classname: Nerd4ever\OidcServerBundle\Entity\SessionEntity
        entity_manager: null
    provider:
        classname: App\CustomIdentityProvider
````

- Integração com o oauth2-server-bundle: A biblioteca se integra com o oauth2-server-bundle para fornecer suporte ao
  OpenID Connect. Certifique-se de ter configurado corretamente o oauth2-server-bundle no seu projeto e ajuste as
  configurações para habilitar o suporte ao OpenID Connect.
- Uso: Após a configuração, você pode começar a usar as funcionalidades do OpenID Connect em seu projeto Symfony.
  Utilize os serviços e APIs fornecidos pela biblioteca para autenticar usuários, gerar tokens de acesso, validar
  tokens, gerenciar sessões e muito mais.

## Contribuição

Se você quiser contribuir com a biblioteca do Nerd4ever OIDC Server Bundle, sinta-se à vontade para enviar pull
requests, relatar problemas

## Agradecimentos

Este projeto foi desenvolvido com base
no [openid-connect-server](https://github.com/steverhoades/oauth2-openid-connect-server) de autoria de Steve Rhoades.
Gostaríamos de expressar nossa sincera gratidão a Steve por disponibilizar seu projeto como código aberto e por suas
contribuições iniciais que serviram como base para este trabalho.

Agradecemos a Steve Rhoades e a todos os contribuidores do projeto openid-connect-server por seu trabalho valioso e sua
dedicação à comunidade de desenvolvimento de software.

Além disso, gostaria de expressar minha profunda gratidão a Dave Gebler pelo excelente tutorial que foi fundamental para
o meu entendimento da configuração do
projeto [thephpleague/oauth2-server-bundle](https://github.com/thephpleague/oauth2-server-bundle). O tutorial publicado
no site [https://davegebler.com](https://davegebler.com/post/coding/build-oauth2-server-php-symfony) foi incrivelmente
útil e esclarecedor.

Também gostaria de agradecer a toda a equipe do [thephpleague](https://oauth2.thephpleague.com/) pela produção
do [oauth2-server-bundle](https://github.com/thephpleague/oauth2-server-bundle)
e [oauth2-server](https://github.com/thephpleague/oauth2-server). Essas ferramentas foram essenciais para a
implementação bem-sucedida da autenticação OAuth2 em meu projeto.

Agradecemos a todos os envolvidos por seu trabalho árduo, dedicação e por disponibilizarem essas valiosas soluções de
código aberto. Sem as contribuições de vocês, meu projeto não seria possível.
