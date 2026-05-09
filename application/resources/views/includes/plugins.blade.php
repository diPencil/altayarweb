@php echo  loadExtension('tawk-chat') @endphp
@php echo  loadExtension('google-analytics') @endphp
@includeWhen(aiChatAssistantEnabled(), 'includes.chat-assistant')
@include('includes.popup-ads')
